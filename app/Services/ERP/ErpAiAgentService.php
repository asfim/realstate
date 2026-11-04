<?php

namespace App\Services\ERP;

use App\Models\ERP\ConSiteStock;
use App\Models\ERP\ConVendor;
use App\Models\ERP\CrmCustomer;
use App\Models\ERP\CrmLead;
use App\Models\ERP\DevBookingInstallment;
use App\Models\ERP\DevProject;
use App\Models\ERP\DevProjectUnit;
use App\Models\ERP\DevSalesBooking;
use App\Models\ERP\HrEmployee;
use App\Models\ERP\HrLaborMusterRoll;
use App\Models\ERP\HrPayroll;
use App\Models\ERP\LandParcel;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ErpAiAgentService
{
    /**
     * Common typo dictionary and phonetic replacements for real estate terms.
     */
    protected array $typoReplacements = [
        // Today & Time variations
        'ajkr' => 'ajker',
        'ajk' => 'ajke',
        'ajka' => 'ajke',
        'aajkr' => 'ajker',
        'ajaker' => 'ajker',
        'aajke' => 'ajke',
        'aajker' => 'ajker',
        'todays' => 'today',
        'tday' => 'today',
        'todai' => 'today',

        // Sales, Selling & Bookings
        'sel' => 'sell',
        'selll' => 'sell',
        'cell' => 'sell',
        'cel' => 'sell',
        'sle' => 'sale',
        'sele' => 'sale',
        'sales' => 'sale',
        'boking' => 'booking',
        'bokng' => 'booking',
        'bookng' => 'booking',
        'bkg' => 'booking',
        'bkng' => 'booking',
        'bokingg' => 'booking',
        'bikri' => 'বিক্রয়',
        'bikroy' => 'বিক্রয়',
        'সেঈল' => 'সেল',
        'সেলল' => 'সেল',

        // Profit, Margin, Loss, Income, Expense
        'profitt' => 'profit',
        'proffit' => 'profit',
        'prophit' => 'profit',
        'prfit' => 'profit',
        'profite' => 'profit',
        'lavh' => 'lav',
        'labh' => 'lav',
        'lab' => 'lav',
        'laav' => 'lav',
        'lavv' => 'lav',
        'incom' => 'income',
        'expens' => 'expense',
        'expence' => 'expense',
        'khoroj' => 'khoroch',
        'khoros' => 'khoroch',
        'koroch' => 'khoroch',
        'khorc' => 'khoroch',
        'লাভ্' => 'লাভ',
        'প্রফিটট' => 'প্রফিট',

        // Dues, Pending, Receivables
        'duee' => 'due',
        'duees' => 'due',
        'dieu' => 'due',
        'dew' => 'due',
        'dwe' => 'due',
        'du' => 'due',
        'bakeya' => 'bakeya',
        'bokeya' => 'bakeya',
        'boccheya' => 'bakeya',
        'bakya' => 'bakeya',
        'kisti' => 'kisti',
        'kisthi' => 'kisti',
        'kisty' => 'kisti',
        'instalment' => 'installment',
        'instalments' => 'installments',
        'বকেয়া' => 'বকেয়া',
        'বকেয়া' => 'বকেয়া',

        // Projects
        'projct' => 'project',
        'projeck' => 'project',
        'projek' => 'project',
        'projeckt' => 'project',
        'porject' => 'project',
        'prjct' => 'project',
        'prj' => 'project',
        'projekts' => 'projects',
        'প্রজেক্টট' => 'প্রজেক্ট',

        // Customers
        'costomer' => 'customer',
        'costomers' => 'customers',
        'custmar' => 'customer',
        'custmars' => 'customers',
        'custmr' => 'customer',
        'kastomer' => 'customer',
        'kustomer' => 'customer',
        'castomar' => 'customer',
        'costomar' => 'customer',
        'customr' => 'customer',

        // Flats / Units
        'flatt' => 'flat',
        'flats' => 'flat',
        'flts' => 'flat',
        'flt' => 'flat',
        'unitt' => 'unit',
        'unite' => 'unit',
        'apertment' => 'apartment',
        'apartmnt' => 'apartment',

        // Vendors / Materials
        'vender' => 'vendor',
        'venders' => 'vendors',
        'vendur' => 'vendor',
        'vendurs' => 'vendors',
        'suplier' => 'supplier',
        'supliers' => 'suppliers',
        'supliyer' => 'supplier',
        'semnt' => 'cement',
        'siment' => 'cement',
        'cemen' => 'cement',
        'ciment' => 'cement',
        'simant' => 'cement',
        'materil' => 'material',
        'metarial' => 'material',
        'stoc' => 'stock',
        'stok' => 'stock',

        // Labor / Muster
        'labour' => 'labor',
        'labourer' => 'laborer',
        'labrer' => 'laborer',
        'hazira' => 'hajira',
        'shromk' => 'shromik',

        // Employees & Payroll
        'employe' => 'employee',
        'empolyee' => 'employee',
        'staf' => 'staff',
        'stafs' => 'staff',
        'slary' => 'salary',
        'sallary' => 'salary',
        'salry' => 'salary',
        'betonn' => 'beton',

        // Land
        'jomii' => 'jomi',
        'landd' => 'land',
        'lnd' => 'land',
        'parsel' => 'parcel',

        // General Bengali Normalization
        'কতো' => 'কত',
        'কয়টা' => 'কয়টা',
    ];

    /**
     * Correct spelling mistakes and typos in query using dictionary and Levenshtein distance.
     */
    public function correctTypos(string $input): string
    {
        // 1. Collapse 3 or more repeated letters into 1 or 2 (e.g. selllll -> sell, dueeee -> due)
        $clean = preg_replace('/(.)\1{2,}/u', '$1$1', $input);

        // Canonical keywords for Levenshtein fuzzy distance matching
        $canonicalWords = [
            'project', 'projects', 'customer', 'customers', 'vendor', 'vendors',
            'cement', 'material', 'labor', 'laborer', 'employee', 'employees',
            'salary', 'payroll', 'booking', 'profit', 'income', 'expense',
            'installment', 'installments', 'apartment', 'parcel'
        ];

        // 2. Tokenize into words preserving spaces and punctuation
        $words = preg_split('/(\s+)/u', $clean, -1, PREG_SPLIT_DELIM_CAPTURE);
        $corrected = [];

        foreach ($words as $w) {
            if (trim($w) === '' || preg_match('/^[^\p{L}\p{N}]+$/u', $w)) {
                $corrected[] = $w;
                continue;
            }

            $lower = mb_strtolower($w, 'UTF-8');

            // Direct dictionary lookup
            if (isset($this->typoReplacements[$lower])) {
                $corrected[] = $this->typoReplacements[$lower];
                continue;
            }

            // Dynamic Levenshtein fuzzy match on English / Latin tokens of length >= 4
            $matched = false;
            if (preg_match('/^[a-z]+$/i', $lower) && strlen($lower) >= 4) {
                foreach ($canonicalWords as $canon) {
                    $dist = levenshtein($lower, $canon);
                    if ($dist <= 2 && abs(strlen($lower) - strlen($canon)) <= 2) {
                        similar_text($lower, $canon, $simPercent);
                        if ($simPercent >= 80) {
                            $corrected[] = $canon;
                            $matched = true;
                            break;
                        }
                    }
                }
            }

            if (!$matched) {
                $corrected[] = $w;
            }
        }

        return implode('', $corrected);
    }

    /**
     * Process an incoming user query and return an AI answer backed by real database facts.
     */
    public function ask(string $rawQuery, ?int $companyId = null, ?int $branchId = null): array
    {
        $query = trim($rawQuery);
        if ($query === '') {
            return [
                'question' => '',
                'intent' => 'empty',
                'language' => 'bn',
                'answer' => "অনুগ্রহ করে আপনার প্রশ্নটি লিখুন। যেমন: *\"আজকের সেল কয়টা হয়েছে?\"*, *\"আজকের লাভ কত?\"*, অথবা *\"কত টাকা বকেয়া আছে?\"*",
                'metrics' => [],
                'suggestions' => $this->getDefaultSuggestions(),
                'powered_by' => 'Aurelia ERP Database Agent',
            ];
        }

        $isBangla = $this->detectBangla($query);

        // Normalize typos and misspellings for ultra-accurate intent detection
        $correctedQuery = $this->correctTypos($query);

        try {
            $intent = $this->detectIntent($correctedQuery);

            // Fetch metrics related to detected intent
            $data = $this->fetchMetricsForIntent($intent, $companyId, $branchId, $correctedQuery);

            // Attempt LLM generation if configured, otherwise use deterministic aggregator
            $apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY'));
            $answer = null;
            $poweredBy = 'Aurelia ERP Database Intelligence';

            if ($apiKey && strlen($apiKey) > 10) {
                $answer = $this->askGemini($correctedQuery, $intent, $data, $apiKey, $isBangla);
                if ($answer) {
                    $poweredBy = 'Gemini 1.5 Flash + Aurelia Database';
                }
            }

            if (!$answer) {
                $answer = $this->formatDeterministicAnswer($intent, $data, $correctedQuery, $isBangla);
            }

            return [
                'question' => $query,
                'intent' => $intent,
                'language' => $isBangla ? 'bn' : 'en',
                'answer' => $answer,
                'metrics' => $data,
                'suggestions' => $this->getRelevantSuggestions($intent),
                'powered_by' => $poweredBy,
            ];
        } catch (\Throwable $e) {
            Log::error('AI Assistant Query Error: ' . $e->getMessage(), [
                'query' => $query,
                'trace' => $e->getTraceAsString(),
            ]);

            // Graceful resilient fallback using basic overview so user always receives an answer
            $fallbackOverview = $this->fetchMetricsForIntent('company_overview', $companyId, $branchId, $query);
            $answer = $this->formatDeterministicAnswer('company_overview', $fallbackOverview, $query, $isBangla);

            return [
                'question' => $query,
                'intent' => 'company_overview',
                'language' => $isBangla ? 'bn' : 'en',
                'answer' => $answer,
                'metrics' => $fallbackOverview,
                'suggestions' => $this->getDefaultSuggestions(),
                'powered_by' => 'Aurelia Resilient Fallback Engine',
            ];
        }
    }

    /**
     * Detect intent from query string.
     */
    public function detectIntent(string $query): string
    {
        $normalized = mb_strtolower($query, 'UTF-8');

        // 1. Specific Code Lookup (BKG-..., LED-..., PRJ-..., VEN-..., EMP-...)
        if (preg_match('/(bkg-[0-9]{4}-[0-9]+|led-[0-9]{4}-[0-9]+|prj-[0-9]{4}-[0-9]+|ven-[0-9]{4}-[0-9]+|emp-[0-9]{4}-[0-9]+)/i', $normalized)) {
            return 'search_code';
        }

        // 2. Projects & Towers ("project koyta", "project list", "aurelia heights", "প্রজেক্ট কয়টা")
        if (
            preg_match('/(project|projects|tower|towers|building|প্রজেক্ট|প্রকল্প|বিল্ডিং)/iu', $normalized) &&
            !preg_match('/(unit|flat|inventory|খালি)/iu', $normalized)
        ) {
            return 'projects_info';
        }

        // 3. Customers ("customer koyjon", "customer list", "গ্রাহক", "কাস্টমার")
        if (
            preg_match('/(customer|customers|client|clients|buyer|buyers|কাস্টমার|গ্রাহক|ক্রেতা)/iu', $normalized) &&
            !preg_match('/(due|dues|baki|কিস্তি|installment)/iu', $normalized)
        ) {
            return 'customers_info';
        }

        // 4. Vendors, Suppliers & Materials ("vendor koyta", "vendor list", "supplier", "ভেন্ডর", "সরবরাহকারী")
        if (
            preg_match('/(vendor|vendors|supplier|suppliers|ভেন্ডর|সরবরাহকারী)/iu', $normalized)
        ) {
            return 'vendors_info';
        }

        // 5. Materials & Construction Stock ("cement", "steel", "stock", "material", "রড", "সিমেন্ট", "ম্যাটেরিয়াল")
        if (
            preg_match('/(cement|steel|rod|sand|material|materials|site stock|স্টক|মালামাল|সিমেন্ট|রড|বালু)/iu', $normalized)
        ) {
            return 'materials_stock';
        }

        // 6. Land Bank ("land", "jomi", "parcel", "katha", "জমি", "প্লট", "মৌজা")
        if (
            preg_match('/(land|jomi|parcel|mouza|katha|khatian|dag|জমি|প্লট|মৌজা|খতিয়ান|দাগ)/iu', $normalized)
        ) {
            return 'land_bank';
        }

        // 7. Unit Pricing ("shobcheye dami", "flats price", "কম দামি", "ফ্ল্যাটের দাম", "price sqft")
        if (
            preg_match('/(price|dam|cost|rate|pricing|dami|sqft price|দামি|মূল্য|দাম|বাজেট).*?(unit|flat|apartment|ফ্ল্যাট|ইউনিট)/iu', $normalized) ||
            preg_match('/(unit|flat|apartment|ফ্ল্যাট|ইউনিট).*?(price|dam|cost|rate|pricing|dami|sqft price|দামি|মূল্য|দাম)/iu', $normalized)
        ) {
            return 'units_pricing';
        }

        // 8. Project Units & Inventory ("unit koyta", "flat koyta", "available flat", "খালি ফ্ল্যাট")
        if (
            preg_match('/(unit(s)?|flat(s)?|apartment(s)?|inventory|stock|available|khali|খালি|ইউনিট|ফ্ল্যাট)/iu', $normalized)
        ) {
            return 'inventory';
        }

        // 9. Daily Site Labor & Muster Roll ("labor", "shromik", "muster", "হাজিরা", "লেবার", "মজুরি")
        if (
            preg_match('/(labor(s)?|laborer(s)?|labour(s)?|shromik|muster|hajira|হাজিরা|লেবার|শ্রমিক|মজুরি|wage(s)?|daily wage)/iu', $normalized)
        ) {
            return 'labor';
        }

        // 10. Sales & Bookings Today ("ajker sell", "today sales", "আজকের সেল", "ajaker sell koyta hoise")
        if (
            preg_match('/(aj(a)?k(e|er|a)|aaj(a)?k(e|er|a)|today|আজকে|আজকের).*?(sel(l)?|sale(s)?|booking(s)?|boking|বিক্রি|বিক্রয়|সেল|বুকিং)/iu', $normalized) ||
            preg_match('/(sel(l)?|sale(s)?|booking(s)?|boking|বিক্রি|বিক্রয়|সেল|বুকিং).*?(aj(a)?k(e|er|a)|aaj(a)?k(e|er|a)|today|আজকে|আজকের)/iu', $normalized)
        ) {
            return 'sales_today';
        }

        // 11. Sales & Bookings General / Monthly / Total ("total sales", "booking list", "মোট বিক্রি")
        if (
            preg_match('/(sel(l)?|sale(s)?|booking(s)?|boking|বিক্রি|বিক্রয়|সেল|বুকিং)/iu', $normalized) &&
            preg_match('/(koyta|koita|koto|how many|total|shob|sob|সব|মোট|মাস|month|list|তালিকা)/iu', $normalized)
        ) {
            return 'sales_summary';
        }

        // 12. Profit / Financials Today ("ajker profit", "ajker lav", "আজকের লাভ", "ajker income")
        if (
            preg_match('/(aj(a)?k(e|er|a)|aaj(a)?k(e|er|a)|today|আজকে|আজকের).*?(profit|\blav\b|\blabh\b|\blab\b|\blos(s)?\b|income|incom|expense|expens|khoro(ch|j|s)|ayon|bey|লাভ|ক্ষতি|ইনকাম|খরচ|প্রফিট|ব্যয়|আয়)/iu', $normalized) ||
            preg_match('/(profit|\blav\b|\blabh\b|\blab\b|\blos(s)?\b|income|incom|expense|expens|khoro(ch|j|s)|লাভ|ক্ষতি|ইনকাম|খরচ|প্রফিট).*?(aj(a)?k(e|er|a)|aaj(a)?k(e|er|a)|today|আজকে|আজকের)/iu', $normalized)
        ) {
            return 'profit_today';
        }

        // 13. Profit / Financials Overall / Monthly ("profit summary", "total income", "মোট লাভ")
        if (
            preg_match('/(profit|\blav\b|\blabh\b|\blab\b|\bloss\b|\blos\b|লাভ|ক্ষতি|প্রফিট)/iu', $normalized) ||
            preg_match('/(income|expense|khoro(ch|j|s)|খরচ|ইনকাম|আয়|ব্যয়).*?(koto|how much|summary|মোট|সব)/iu', $normalized)
        ) {
            return 'profit_summary';
        }

        // 14. Dues / Receivables / Installments ("due koto", "baki koto", "বকেয়া কত", "কিস্তি বকেয়া")
        if (
            preg_match('/(due(s)?|baki|bakeya|bokeya|boccheya|বকেয়া|বকেয়া|বাকি|ডিউ|কিস্তি|installment(s)?|kisti|receivable(s)?|payment pending)/iu', $normalized)
        ) {
            return 'dues';
        }

        // 15. CRM Leads & Inquiries ("lead koyta", "leads list", "লিড", "অনুসন্ধান")
        if (
            preg_match('/(lead(s)?|inquiry|inquiries|customer inquiry|নতুন গ্রাহক|লিড|অনুসন্ধান)/iu', $normalized)
        ) {
            return 'leads';
        }

        // 16. Permanent Employees & Payroll ("employee koyjon", "staff list", "বেতন", "কর্মচারী", "salary")
        if (
            preg_match('/(employee(s)?|staff(s)?|kormochari|কর্মচারী|কর্মকর্তা|payroll(s)?|salary|বেতন)/iu', $normalized)
        ) {
            return 'employees';
        }

        // 17. Transactions & Bank / Cash Movements ("transaction", "লেনদেন", "টাকা কোথায় গেল")
        if (
            preg_match('/(transaction|transactions|payment|payments|bank|cash|লেনদেন|পেমেন্ট|ক্যাশ|ব্যাংক)/iu', $normalized)
        ) {
            return 'transactions_info';
        }

        // 18. Smart Entity / Name Search Check:
        // If query mentions an explicit person name or specific keyword like "Hasan", "Rahim", "Gulshan", "Aurelia", "Parvez"
        $words = preg_split('/[\s,\.\?\!]+/u', $query, -1, PREG_SPLIT_NO_EMPTY);
        $stopWords = ['ki', 'koto', 'koyta', 'koita', 'ase', 'ache', 'hoise', 'holo', 'kemon', 'amader', 'er', 'theke', 'dao', 'bolo', 'bistarito', 'info', 'details', 'tell', 'me', 'about', 'how', 'many', 'much', 'is', 'the', 'what', 'are'];
        foreach ($words as $w) {
            $cleaned = mb_strtolower($w, 'UTF-8');
            if (mb_strlen($cleaned) >= 3 && !in_array($cleaned, $stopWords)) {
                // Check if this keyword exists in Customer, Lead, Project, or Vendor names
                if (
                    CrmCustomer::where('name', 'like', "%{$cleaned}%")->orWhere('phone', 'like', "%{$cleaned}%")->exists() ||
                    CrmLead::where('name', 'like', "%{$cleaned}%")->orWhere('phone', 'like', "%{$cleaned}%")->exists() ||
                    DevProject::where('name', 'like', "%{$cleaned}%")->exists() ||
                    ConVendor::where('name', 'like', "%{$cleaned}%")->exists()
                ) {
                    return 'smart_entity_search';
                }
            }
        }

        // 19. Help / Commands
        if (
            preg_match('/(help|sahajjo|command|questions|ki ki|কী কী|সাহায্য|guide|কিভাবে)/iu', $normalized)
        ) {
            return 'help';
        }

        // 20. Executive Overview / Summary
        if (
            preg_match('/(summary|overview|obostha|dashboard|status|সারসংক্ষেপ|সার্বিক|অবস্থা|রিপোর্ট|report|সবকিছু|overall)/iu', $normalized)
        ) {
            return 'company_overview';
        }

        // Default to smart entity search if we can find anything, else company overview
        return 'smart_entity_search';
    }

    /**
     * Detect if the query is in Bangla or Banglish.
     */
    protected function detectBangla(string $query): bool
    {
        // Bengali Unicode range: \x{0980}-\x{09FF}
        if (preg_match('/[\x{0980}-\x{09FF}]/u', $query)) {
            return true;
        }

        // Common Banglish words
        $banglishTerms = ['ajke', 'ajker', 'ajaker', 'aajke', 'aajker', 'koto', 'koyta', 'koita', 'baki', 'bakeya', 'bokeya', 'lav', 'labh', 'khoroch', 'ase', 'hoise', 'hobe', 'kemon', 'shob', 'sob', 'kisti', 'taka', 'amader', 'dao', 'bolo'];
        $lower = mb_strtolower($query);
        foreach ($banglishTerms as $term) {
            if (str_contains($lower, $term)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gather database facts based on intent.
     */
    public function fetchMetricsForIntent(string $intent, ?int $companyId = null, ?int $branchId = null, string $rawQuery = ''): array
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        switch ($intent) {
            case 'search_code':
                preg_match('/(bkg-[0-9]{4}-[0-9]+|led-[0-9]{4}-[0-9]+|prj-[0-9]{4}-[0-9]+|ven-[0-9]{4}-[0-9]+|emp-[0-9]{4}-[0-9]+)/i', $rawQuery, $matches);
                $code = strtoupper($matches[1] ?? '');

                if (str_starts_with($code, 'BKG')) {
                    $b = DevSalesBooking::with(['customer', 'project', 'unit', 'installments'])->where('booking_code', $code)->first();
                    if ($b) {
                        return [
                            'type' => 'booking',
                            'code' => $code,
                            'found' => true,
                            'customer' => $b->customer->name ?? 'N/A',
                            'phone' => $b->customer->phone ?? 'N/A',
                            'project' => $b->project->name ?? 'N/A',
                            'unit' => $b->unit->unit_number ?? 'N/A',
                            'contract_amount' => (float) $b->total_contract_amount,
                            'downpayment' => (float) $b->downpayment_amount,
                            'status' => $b->status,
                            'booking_date' => $b->booking_date ? $b->booking_date->format('Y-m-d') : 'N/A',
                            'installments_count' => $b->installments->count(),
                            'balance_due' => $b->balanceDue(),
                        ];
                    }
                } elseif (str_starts_with($code, 'LED')) {
                    $l = CrmLead::with(['featuredProject', 'property'])->where('lead_code', $code)->first();
                    if ($l) {
                        return [
                            'type' => 'lead',
                            'code' => $code,
                            'found' => true,
                            'name' => $l->name,
                            'phone' => $l->phone,
                            'stage' => $l->stage,
                            'source' => $l->source,
                            'priority' => $l->priority,
                            'project' => $l->featuredProject->title ?? 'General',
                            'date' => $l->created_at->format('Y-m-d'),
                        ];
                    }
                } elseif (str_starts_with($code, 'PRJ')) {
                    $p = DevProject::where('project_code', $code)->first();
                    if ($p) {
                        return [
                            'type' => 'project',
                            'code' => $code,
                            'found' => true,
                            'name' => $p->name,
                            'location' => $p->location,
                            'total_units' => $p->total_units,
                            'status' => $p->status,
                        ];
                    }
                }
                return ['type' => 'unknown', 'code' => $code, 'found' => false];

            case 'projects_info':
                $projects = DevProject::withCount([
                    'units as total_units_count',
                    'units as available_units_count' => fn($q) => $q->where('status', 'available'),
                    'units as booked_units_count' => fn($q) => $q->where('status', 'booked'),
                    'units as sold_units_count' => fn($q) => $q->where('status', 'sold'),
                ])->get();

                return [
                    'count' => $projects->count(),
                    'projects' => $projects->map(fn($p) => [
                        'code' => $p->project_code,
                        'name' => $p->name,
                        'location' => $p->location ?? $p->address ?? 'N/A',
                        'type' => $p->project_type ?? 'Residential',
                        'total_units' => $p->total_units ?? $p->total_units_count,
                        'available' => $p->available_units_count,
                        'booked' => $p->booked_units_count,
                        'sold' => $p->sold_units_count,
                        'floors' => $p->total_floors,
                        'status' => $p->status,
                    ])->toArray(),
                ];

            case 'customers_info':
                $customers = CrmCustomer::with(['company'])->latest()->take(10)->get();
                $totalCustomers = CrmCustomer::count();

                return [
                    'total_count' => $totalCustomers,
                    'customers' => $customers->map(fn($c) => [
                        'code' => $c->customer_code,
                        'name' => $c->name,
                        'phone' => $c->phone,
                        'email' => $c->email,
                        'status' => $c->status,
                        'nid' => $c->nid_number ?? 'N/A',
                    ])->toArray(),
                ];

            case 'vendors_info':
                $vendors = ConVendor::latest()->take(10)->get();
                $totalVendors = ConVendor::count();

                return [
                    'total_count' => $totalVendors,
                    'vendors' => $vendors->map(fn($v) => [
                        'code' => $v->vendor_code,
                        'name' => $v->name,
                        'contact_person' => $v->contact_person ?? 'N/A',
                        'phone' => $v->phone,
                        'status' => $v->status,
                    ])->toArray(),
                ];

            case 'materials_stock':
                $stocks = ConSiteStock::with(['project'])->get();

                return [
                    'count' => $stocks->count(),
                    'stocks' => $stocks->map(fn($s) => [
                        'material' => $s->material_name,
                        'project' => $s->project->name ?? 'Main Site',
                        'current_qty' => (float) $s->current_stock_qty,
                        'unit' => $s->unit_of_measure,
                        'total_received' => (float) $s->total_received_qty,
                        'total_consumed' => (float) $s->total_consumed_qty,
                    ])->toArray(),
                ];

            case 'land_bank':
                $parcels = LandParcel::all();

                return [
                    'total_parcels' => $parcels->count(),
                    'total_katha' => (float) $parcels->sum('total_area_katha'),
                    'parcels' => $parcels->map(fn($lp) => [
                        'code' => $lp->parcel_code,
                        'title' => $lp->title,
                        'mouza' => $lp->mouza,
                        'katha' => (float) $lp->total_area_katha,
                        'ratio' => "{$lp->landowner_ratio}% / {$lp->developer_ratio}%",
                        'status' => $lp->status,
                    ])->toArray(),
                ];

            case 'units_pricing':
                $units = DevProjectUnit::with(['project'])->orderBy('grand_total_price', 'desc')->take(10)->get();
                $avgSqftPrice = (float) DevProjectUnit::avg('base_price_sqft');
                $highestPrice = (float) DevProjectUnit::max('grand_total_price');
                $lowestPrice = (float) DevProjectUnit::min('grand_total_price');

                return [
                    'highest_price' => $highestPrice,
                    'lowest_price' => $lowestPrice,
                    'avg_sqft_price' => $avgSqftPrice,
                    'units' => $units->map(fn($u) => [
                        'unit_number' => $u->unit_number,
                        'project' => $u->project->name ?? 'Project',
                        'bedrooms' => $u->bedrooms,
                        'sqft' => (float) $u->gross_area_sqft,
                        'base_rate' => (float) $u->base_price_sqft,
                        'total_price' => (float) $u->grand_total_price,
                        'status' => $u->status,
                    ])->toArray(),
                ];

            case 'transactions_info':
                $recent = Transaction::with(['category'])->latest('transaction_date')->take(10)->get();
                $todayIncome = (float) Transaction::income()->whereDate('transaction_date', $today)->sum('amount');
                $todayExpense = (float) Transaction::expense()->whereDate('transaction_date', $today)->sum('amount');

                return [
                    'today_income' => $todayIncome,
                    'today_expense' => $todayExpense,
                    'transactions' => $recent->map(fn($t) => [
                        'type' => $t->type,
                        'title' => $t->title,
                        'amount' => (float) $t->amount,
                        'category' => $t->category->name ?? 'General',
                        'method' => $t->payment_method,
                        'date' => $t->transaction_date ? $t->transaction_date->format('Y-m-d') : 'N/A',
                    ])->toArray(),
                ];

            case 'smart_entity_search':
                // Extract searchable search terms from the query
                $words = preg_split('/[\s,\.\?\!]+/u', $rawQuery, -1, PREG_SPLIT_NO_EMPTY);
                $stopWords = ['ki', 'koto', 'koyta', 'koita', 'ase', 'ache', 'hoise', 'holo', 'kemon', 'amader', 'er', 'theke', 'dao', 'bolo', 'details', 'tell', 'me', 'about', 'how', 'many', 'much', 'is', 'the', 'what', 'are', 'bistarito', 'info'];
                $searchTokens = [];

                foreach ($words as $w) {
                    $cleaned = mb_strtolower($w, 'UTF-8');
                    if (mb_strlen($cleaned) >= 3 && !in_array($cleaned, $stopWords)) {
                        $searchTokens[] = $w;
                    }
                }

                if (empty($searchTokens)) {
                    $searchTokens = [$rawQuery];
                }

                $soundexTokens = array_map('soundex', $searchTokens);

                $matchFuzzy = function($name, $phone = '') use ($searchTokens, $soundexTokens) {
                    $nameLower = mb_strtolower($name, 'UTF-8');
                    foreach ($searchTokens as $i => $tok) {
                        $tokLower = mb_strtolower($tok, 'UTF-8');
                        if (str_contains($nameLower, $tokLower)) return true;
                        if ($phone && str_contains($phone, $tok)) return true;
                        foreach (explode(' ', $name) as $part) {
                            $partClean = trim(mb_strtolower($part, 'UTF-8'));
                            if (mb_strlen($partClean) >= 3) {
                                if (levenshtein($partClean, $tokLower) <= 1) return true;
                                if (soundex($partClean) === $soundexTokens[$i]) return true;
                            }
                        }
                    }
                    return false;
                };

                $foundCustomers = CrmCustomer::all()->filter(fn($c) => $matchFuzzy($c->name, $c->phone));
                $foundLeads = CrmLead::all()->filter(fn($l) => $matchFuzzy($l->name, $l->phone));
                $foundProjects = DevProject::all()->filter(fn($p) => $matchFuzzy($p->name . ' ' . ($p->location ?? '')));
                $foundUnits = DevProjectUnit::all()->filter(fn($u) => $matchFuzzy((string) $u->unit_number));
                $foundEmployees = HrEmployee::all()->filter(fn($e) => $matchFuzzy($e->name, $e->phone));
                $foundVendors = ConVendor::all()->filter(fn($v) => $matchFuzzy($v->name . ' ' . ($v->contact_person ?? ''), $v->phone));

                return [
                    'search_term' => implode(' ', $searchTokens),
                    'customers' => $foundCustomers->map(fn($c) => ['name' => $c->name, 'code' => $c->customer_code, 'phone' => $c->phone, 'status' => $c->status])->values()->toArray(),
                    'leads' => $foundLeads->map(fn($l) => ['name' => $l->name, 'code' => $l->lead_code, 'phone' => $l->phone, 'stage' => $l->stage])->values()->toArray(),
                    'projects' => $foundProjects->map(fn($p) => ['name' => $p->name, 'code' => $p->project_code, 'location' => $p->location, 'status' => $p->status])->values()->toArray(),
                    'units' => $foundUnits->map(fn($u) => ['unit' => $u->unit_number, 'sqft' => (float)$u->gross_area_sqft, 'price' => (float)$u->grand_total_price, 'status' => $u->status])->values()->toArray(),
                    'employees' => $foundEmployees->map(fn($e) => ['name' => $e->name, 'phone' => $e->phone, 'salary' => (float)$e->gross_salary, 'status' => $e->status])->values()->toArray(),
                    'vendors' => $foundVendors->map(fn($v) => ['name' => $v->name, 'phone' => $v->phone, 'contact' => $v->contact_person])->values()->toArray(),
                ];

            case 'sales_today':
                $query = DevSalesBooking::with(['customer', 'project', 'unit'])
                    ->where(function ($q) use ($today) {
                        $q->whereDate('booking_date', $today)
                          ->orWhereDate('created_at', $today);
                    });
                if ($companyId) $query->where('company_id', $companyId);
                if ($branchId) $query->where('branch_id', $branchId);

                $todayBookings = $query->get();
                $totalContract = (float) $todayBookings->sum('total_contract_amount');
                $totalDownpayment = (float) $todayBookings->sum('downpayment_amount');

                $monthCount = DevSalesBooking::whereMonth('booking_date', $today->month)
                    ->whereYear('booking_date', $today->year)
                    ->count();

                return [
                    'date' => $today->format('Y-m-d'),
                    'count' => $todayBookings->count(),
                    'total_contract_amount' => $totalContract,
                    'total_downpayment' => $totalDownpayment,
                    'bookings' => $todayBookings->map(fn($b) => [
                        'code' => $b->booking_code,
                        'customer' => $b->customer->name ?? 'N/A',
                        'phone' => $b->customer->phone ?? '',
                        'project' => $b->project->name ?? 'N/A',
                        'unit' => $b->unit->unit_number ?? 'N/A',
                        'amount' => (float) $b->total_contract_amount,
                        'downpayment' => (float) $b->downpayment_amount,
                        'status' => $b->status,
                    ])->toArray(),
                    'month_total_bookings' => $monthCount,
                ];

            case 'sales_summary':
                $totalBookings = DevSalesBooking::count();
                $monthBookings = DevSalesBooking::whereMonth('booking_date', $today->month)
                    ->whereYear('booking_date', $today->year)
                    ->count();
                $totalValue = (float) DevSalesBooking::sum('total_contract_amount');
                $recent = DevSalesBooking::with(['customer', 'project', 'unit'])
                    ->latest()
                    ->take(5)
                    ->get();

                return [
                    'total_count' => $totalBookings,
                    'this_month_count' => $monthBookings,
                    'total_contract_value' => $totalValue,
                    'recent_bookings' => $recent->map(fn($b) => [
                        'code' => $b->booking_code,
                        'customer' => $b->customer->name ?? 'N/A',
                        'project' => $b->project->name ?? 'N/A',
                        'unit' => $b->unit->unit_number ?? 'N/A',
                        'amount' => (float) $b->total_contract_amount,
                        'date' => $b->booking_date ? $b->booking_date->format('Y-m-d') : $b->created_at->format('Y-m-d'),
                        'status' => $b->status,
                    ])->toArray(),
                ];

            case 'profit_today':
                $todayIncome = (float) Transaction::income()->whereDate('transaction_date', $today)->sum('amount');
                $todayExpense = (float) Transaction::expense()->whereDate('transaction_date', $today)->sum('amount');
                $todayLaborWage = (float) HrLaborMusterRoll::whereDate('work_date', $today)->sum('total_wage');
                $totalTodayExpense = $todayExpense + $todayLaborWage;
                $todayNetProfit = $todayIncome - $totalTodayExpense;

                $monthIncome = (float) Transaction::income()->whereBetween('transaction_date', [$startOfMonth, $today])->sum('amount');
                $monthExpense = (float) Transaction::expense()->whereBetween('transaction_date', [$startOfMonth, $today])->sum('amount');
                $monthLabor = (float) HrLaborMusterRoll::whereBetween('work_date', [$startOfMonth, $today])->sum('total_wage');
                $monthPayroll = (float) HrPayroll::where('salary_month', $today->month)->where('salary_year', $today->year)->sum('net_payable');
                $monthNetProfit = $monthIncome - ($monthExpense + $monthLabor + $monthPayroll);

                return [
                    'date' => $today->format('Y-m-d'),
                    'today_income' => $todayIncome,
                    'today_expense' => $todayExpense,
                    'today_labor_wage' => $todayLaborWage,
                    'today_total_expense' => $totalTodayExpense,
                    'today_net_profit' => $todayNetProfit,
                    'month_income' => $monthIncome,
                    'month_expense' => $monthExpense + $monthLabor + $monthPayroll,
                    'month_net_profit' => $monthNetProfit,
                ];

            case 'profit_summary':
                $totalIncome = (float) Transaction::income()->sum('amount');
                $totalExpense = (float) Transaction::expense()->sum('amount');
                $totalLabor = (float) HrLaborMusterRoll::sum('total_wage');
                $totalPayroll = (float) HrPayroll::sum('net_payable');
                $totalOutflow = $totalExpense + $totalLabor + $totalPayroll;
                $netProfit = $totalIncome - $totalOutflow;

                $monthIncome = (float) Transaction::income()->whereBetween('transaction_date', [$startOfMonth, $today])->sum('amount');
                $monthExpense = (float) Transaction::expense()->whereBetween('transaction_date', [$startOfMonth, $today])->sum('amount');

                return [
                    'total_income' => $totalIncome,
                    'total_expense' => $totalExpense,
                    'total_labor_wages' => $totalLabor,
                    'total_payroll' => $totalPayroll,
                    'total_outflow' => $totalOutflow,
                    'net_profit' => $netProfit,
                    'margin_percent' => $totalIncome > 0 ? round(($netProfit / $totalIncome) * 100, 2) : 0,
                    'this_month_income' => $monthIncome,
                    'this_month_expense' => $monthExpense,
                ];

            case 'dues':
                $unpaid = DevBookingInstallment::with(['booking.customer', 'booking.project', 'booking.unit'])
                    ->whereIn('status', ['pending', 'partially_paid']);

                $totalDue = (float) (clone $unpaid)->sum('balance_due');
                $overdueAmount = (float) (clone $unpaid)->where('due_date', '<', $today)->sum('balance_due');
                $overdueCount = (clone $unpaid)->where('due_date', '<', $today)->count();
                $upcomingAmount = (float) (clone $unpaid)->whereBetween('due_date', [$today, $today->copy()->addDays(30)])->sum('balance_due');

                $topPending = (clone $unpaid)
                    ->orderBy('due_date', 'asc')
                    ->take(5)
                    ->get()
                    ->map(fn($inst) => [
                        'milestone' => $inst->milestone_name ?? 'Installment #' . $inst->installment_number,
                        'customer' => $inst->booking->customer->name ?? 'N/A',
                        'phone' => $inst->booking->customer->phone ?? '',
                        'project' => $inst->booking->project->name ?? 'N/A',
                        'unit' => $inst->booking->unit->unit_number ?? 'N/A',
                        'balance_due' => (float) $inst->balance_due,
                        'due_date' => $inst->due_date ? $inst->due_date->format('Y-m-d') : 'N/A',
                        'is_overdue' => $inst->due_date && $inst->due_date->lt($today),
                    ])->toArray();

                return [
                    'total_due' => $totalDue,
                    'overdue_amount' => $overdueAmount,
                    'overdue_count' => $overdueCount,
                    'upcoming_30_days_due' => $upcomingAmount,
                    'pending_installments' => $topPending,
                ];

            case 'leads':
                $totalLeads = CrmLead::count();
                $todayLeads = CrmLead::whereDate('created_at', $today)->count();
                $byStage = CrmLead::selectRaw('stage, count(*) as total')
                    ->groupBy('stage')
                    ->pluck('total', 'stage')
                    ->toArray();

                $recentLeads = CrmLead::with(['featuredProject', 'property'])
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(fn($l) => [
                        'code' => $l->lead_code,
                        'name' => $l->name,
                        'phone' => $l->phone,
                        'stage' => $l->stage,
                        'source' => $l->source,
                        'priority' => $l->priority,
                        'project' => $l->featuredProject->title ?? 'General',
                        'date' => $l->created_at->format('Y-m-d'),
                    ])->toArray();

                return [
                    'today_leads' => $todayLeads,
                    'total_leads' => $totalLeads,
                    'by_stage' => $byStage,
                    'recent_leads' => $recentLeads,
                ];

            case 'inventory':
                $totalUnits = DevProjectUnit::count();
                $availableUnits = DevProjectUnit::where('status', 'available')->count();
                $bookedUnits = DevProjectUnit::where('status', 'booked')->count();
                $soldUnits = DevProjectUnit::where('status', 'sold')->count();

                $projectBreakdown = DevProject::withCount([
                    'units as total_units',
                    'units as available_units' => fn($q) => $q->where('status', 'available'),
                    'units as booked_units' => fn($q) => $q->where('status', 'booked'),
                    'units as sold_units' => fn($q) => $q->where('status', 'sold'),
                ])->get()->map(fn($p) => [
                    'project' => $p->name,
                    'total' => $p->total_units,
                    'available' => $p->available_units,
                    'booked' => $p->booked_units,
                    'sold' => $p->sold_units,
                ])->toArray();

                return [
                    'total_units' => $totalUnits,
                    'available_units' => $availableUnits,
                    'booked_units' => $bookedUnits,
                    'sold_units' => $soldUnits,
                    'projects' => $projectBreakdown,
                ];

            case 'labor':
                $todayMuster = HrLaborMusterRoll::with(['laborer', 'project'])
                    ->whereDate('work_date', $today)
                    ->get();

                $latestMusterDate = HrLaborMusterRoll::max('work_date');
                $latestMuster = $latestMusterDate ? HrLaborMusterRoll::with(['laborer', 'project'])
                    ->whereDate('work_date', $latestMusterDate)
                    ->get() : collect();

                return [
                    'today_count' => $todayMuster->count(),
                    'today_wages' => (float) $todayMuster->sum('total_wage'),
                    'latest_date' => $latestMusterDate,
                    'latest_count' => $latestMuster->count(),
                    'latest_wages' => (float) $latestMuster->sum('total_wage'),
                    'total_all_time_wages' => (float) HrLaborMusterRoll::sum('total_wage'),
                ];

            case 'employees':
                $totalEmployees = HrEmployee::where('status', 'active')->count();
                $employeesList = HrEmployee::with(['department', 'designation'])->take(10)->get();
                $totalPayrollsPaid = (float) HrPayroll::where('payment_status', 'paid')->sum('net_payable');
                $pendingPayrolls = (float) HrPayroll::where('payment_status', 'pending')->sum('net_payable');

                return [
                    'active_employees' => $totalEmployees,
                    'total_payroll_paid' => $totalPayrollsPaid,
                    'pending_payroll_approval' => $pendingPayrolls,
                    'employees' => $employeesList->map(fn($e) => [
                        'code' => $e->employee_code,
                        'name' => $e->name,
                        'designation' => $e->designation->name ?? 'Staff',
                        'department' => $e->department->name ?? 'General',
                        'gross_salary' => (float) $e->gross_salary,
                        'phone' => $e->phone,
                    ])->toArray(),
                ];

            case 'help':
                return [
                    'supported_topics' => [
                        'Projects & Towers' => ['আমাদের কয়টা প্রজেক্ট আছে?', 'প্রজেক্ট লিস্ট দাও', 'where are the projects?'],
                        'Sales & Bookings' => ['আজকের সেল কয়টা হয়েছে?', 'মোট কয়টা বুকিং আছে?', 'today sales'],
                        'Profit & Loss' => ['আজকের লাভ কত?', 'চলতি মাসের মোট ইনকাম ও এক্সপেন্স কত?', 'profit summary'],
                        'Dues & Receivables' => ['কত টাকা বকেয়া আছে?', 'কার কার কিস্তি বাকি আছে?', 'total dues'],
                        'CRM Leads' => ['আজকে কয়টা লিড এসেছে?', 'মোট লিডের সংখ্যা কত?', 'crm leads status'],
                        'Customers' => ['কাস্টমার কয়জন আছে?', 'কাস্টমারদের তালিকা', 'customer list'],
                        'Flats / Pricing' => ['ইউনিট কয়টা খালি আছে?', 'সবচেয়ে দামি ফ্ল্যাট কোনটা?', 'available units'],
                        'Vendors & Materials' => ['ভেন্ডর কয়টা আছে?', 'সিমেন্ট ও রড স্টক কত?', 'vendor list'],
                        'Construction Labor' => ['আজকের সাইট লেবার হাজিরা কত?', 'লেবারদের দৈনিক মজুরি কত?', 'daily labor muster'],
                        'Employees & Payroll' => ['কর্মচারী কয়জন আছে?', 'স্টাফদের বেতন কত?', 'employee list'],
                        'Land Bank' => ['জমির পরিমাণ কত?', 'ল্যান্ড পার্সেল কয়টা আছে?', 'land parcels'],
                        'Search by Name' => ['Hasan Parvez এর তথ্য', 'BKG-2026-00001', 'Aurelia Heights'],
                    ],
                ];

            case 'company_overview':
            default:
                $todaySalesCount = DevSalesBooking::whereDate('booking_date', $today)->orWhereDate('created_at', $today)->count();
                $todaySalesVal = (float) DevSalesBooking::whereDate('booking_date', $today)->orWhereDate('created_at', $today)->sum('total_contract_amount');
                $todayIncome = (float) Transaction::income()->whereDate('transaction_date', $today)->sum('amount');
                $todayExpense = (float) Transaction::expense()->whereDate('transaction_date', $today)->sum('amount');
                $totalDue = (float) DevBookingInstallment::whereIn('status', ['pending', 'partially_paid'])->sum('balance_due');
                $overdue = (float) DevBookingInstallment::whereIn('status', ['pending', 'partially_paid'])->where('due_date', '<', $today)->sum('balance_due');
                $totalUnits = DevProjectUnit::count();
                $availableUnits = DevProjectUnit::where('status', 'available')->count();
                $todayLeads = CrmLead::whereDate('created_at', $today)->count();
                $totalLeads = CrmLead::count();
                $totalProjects = DevProject::count();
                $totalCustomers = CrmCustomer::count();

                return [
                    'date' => $today->format('Y-m-d'),
                    'today_sales_count' => $todaySalesCount,
                    'today_sales_value' => $todaySalesVal,
                    'today_income' => $todayIncome,
                    'today_expense' => $todayExpense,
                    'today_profit' => $todayIncome - $todayExpense,
                    'total_dues' => $totalDue,
                    'overdue_dues' => $overdue,
                    'total_units' => $totalUnits,
                    'available_units' => $availableUnits,
                    'today_leads' => $todayLeads,
                    'total_leads' => $totalLeads,
                    'total_projects' => $totalProjects,
                    'total_customers' => $totalCustomers,
                ];
        }
    }

    /**
     * Format deterministic markdown response based on aggregated data.
     */
    protected function formatDeterministicAnswer(string $intent, array $data, string $query, bool $isBangla): string
    {
        $fmt = fn($num) => 'BDT ' . number_format((float) $num, 2);

        switch ($intent) {
            case 'search_code':
                if (empty($data['found'])) {
                    return $isBangla
                        ? "### 🔍 অনুসন্ধানের ফলাফল\n\n> দুঃখিত, কোড `{$data['code']}` সম্পর্কিত কোনো রেকর্ড ডাটাবেসে পাওয়া যায়নি।"
                        : "### 🔍 Search Result\n\n> Sorry, no record found matching code `{$data['code']}`.";
                }
                if ($data['type'] === 'booking') {
                    $cVal = $fmt($data['contract_amount']);
                    $bDue = $fmt($data['balance_due']);
                    $dPay = $fmt($data['downpayment']);
                    return $isBangla
                        ? "### 📋 বুকিং বিবরণ: `{$data['code']}`\n\n" .
                          "| ক্ষেত্র | তথ্য |\n" .
                          "| :--- | :--- |\n" .
                          "| 👤 **গ্রাহকের নাম** | {$data['customer']} ({$data['phone']}) |\n" .
                          "| 🏢 **প্রকল্প ও ইউনিট** | {$data['project']} (Unit {$data['unit']}) |\n" .
                          "| 💰 **চুক্তি মূল্য** | **{$cVal}** |\n" .
                          "| 💵 **ডাউনপেমেন্ট** | {$dPay} |\n" .
                          "| ⚠️ **বর্তমান বকেয়া** | **{$bDue}** |\n" .
                          "| 📅 **বুকিং তারিখ** | {$data['booking_date']} |\n" .
                          "| 📌 **স্ট্যাটাস** | <span class=\"badge bg-success\">{$data['status']}</span> |\n"
                        : "### 📋 Booking Details: `{$data['code']}`\n\n" .
                          "| Field | Information |\n" .
                          "| :--- | :--- |\n" .
                          "| 👤 **Customer** | {$data['customer']} ({$data['phone']}) |\n" .
                          "| 🏢 **Project & Unit** | {$data['project']} (Unit {$data['unit']}) |\n" .
                          "| 💰 **Contract Value** | **{$cVal}** |\n" .
                          "| 💵 **Downpayment** | {$dPay} |\n" .
                          "| ⚠️ **Current Balance Due** | **{$bDue}** |\n" .
                          "| 📅 **Booking Date** | {$data['booking_date']} |\n" .
                          "| 📌 **Status** | <span class=\"badge bg-success\">{$data['status']}</span> |\n";
                } elseif ($data['type'] === 'lead') {
                    return $isBangla
                        ? "### 👥 লিড বিবরণ: `{$data['code']}`\n\n" .
                          "| ক্ষেত্র | তথ্য |\n" .
                          "| :--- | :--- |\n" .
                          "| 👤 **নাম** | {$data['name']} |\n" .
                          "| 📞 **মোবাইল** | {$data['phone']} |\n" .
                          "| 🎯 **স্টেজ** | <span class=\"badge bg-info text-dark\">{$data['stage']}</span> |\n" .
                          "| 🌐 **সোর্স** | {$data['source']} |\n" .
                          "| ⚡ **অগ্রাধিকার** | {$data['priority']} |\n" .
                          "| 🏢 **প্রজেক্ট** | {$data['project']} |\n" .
                          "| 📅 **তৈরির তারিখ** | {$data['date']} |\n"
                        : "### 👥 Lead Details: `{$data['code']}`\n\n" .
                          "| Field | Information |\n" .
                          "| :--- | :--- |\n" .
                          "| 👤 **Name** | {$data['name']} |\n" .
                          "| 📞 **Phone** | {$data['phone']} |\n" .
                          "| 🎯 **Stage** | <span class=\"badge bg-info text-dark\">{$data['stage']}</span> |\n" .
                          "| 🌐 **Source** | {$data['source']} |\n" .
                          "| ⚡ **Priority** | {$data['priority']} |\n" .
                          "| 🏢 **Project** | {$data['project']} |\n" .
                          "| 📅 **Created Date** | {$data['date']} |\n";
                } elseif ($data['type'] === 'project') {
                    return "### 🏢 প্রজেক্ট বিবরণ: `{$data['code']}`\n\n- নাম: **{$data['name']}**\n- লোকেশন: **{$data['location']}**\n- মোট ইউনিট: **{$data['total_units']} টি**\n- স্ট্যাটাস: **{$data['status']}**";
                }
                return "Record found for code: {$data['code']}";

            case 'projects_info':
                $count = $data['count'] ?? 0;
                $out = $isBangla
                    ? "### 🏗️ চলমান রিয়েল এস্টেট প্রকল্পসমূহ (মোট: {$count} টি)\n\n" .
                      "| কোড | প্রকল্পের নাম | লোকেশন | মোট ইউনিট | খালি (Available) | বুকড | স্ট্যাটাস |\n" .
                      "| :--- | :--- | :--- | :--- | :--- | :--- | :--- |\n"
                    : "### 🏗️ Real Estate Projects Register (Total: {$count})\n\n" .
                      "| Code | Project Name | Location | Units | Available | Booked | Status |\n" .
                      "| :--- | :--- | :--- | :--- | :--- | :--- | :--- |\n";

                foreach ($data['projects'] ?? [] as $p) {
                    $out .= "| `{$p['code']}` | **{$p['name']}** | {$p['location']} | {$p['total_units']} টি | <span class=\"badge bg-success\">{$p['available']}</span> | {$p['booked']} | <span class=\"badge bg-primary\">{$p['status']}</span> |\n";
                }
                return $out;

            case 'customers_info':
                $count = $data['total_count'] ?? 0;
                $out = $isBangla
                    ? "### 👥 নিবন্ধিত কাস্টমার মাস্টার তালিকা (সর্বমোট: {$count} জন)\n\n" .
                      "| আইডি | কাস্টমারের নাম | মোবাইল | ইমেইল | স্ট্যাটাস |\n" .
                      "| :--- | :--- | :--- | :--- | :--- |\n"
                    : "### 👥 Customer Master Directory (Total: {$count})\n\n" .
                      "| Customer Code | Full Name | Phone | Email | Status |\n" .
                      "| :--- | :--- | :--- | :--- | :--- |\n";

                foreach ($data['customers'] ?? [] as $c) {
                    $out .= "| `{$c['code']}` | **{$c['name']}** | {$c['phone']} | {$c['email']} | <span class=\"badge bg-success\">{$c['status']}</span> |\n";
                }
                return $out;

            case 'vendors_info':
                $count = $data['total_count'] ?? 0;
                $out = $isBangla
                    ? "### 🚚 সরবরাহকারী ও ভেন্ডর তালিকা (মোট: {$count} টি)\n\n" .
                      "| কোড | ভেন্ডরের নাম | যোগাযোগ ব্যক্তি | মোবাইল | স্ট্যাটাস |\n" .
                      "| :--- | :--- | :--- | :--- | :--- |\n"
                    : "### 🚚 Construction Vendors & Suppliers (Total: {$count})\n\n" .
                      "| Code | Vendor Name | Contact Person | Phone | Status |\n" .
                      "| :--- | :--- | :--- | :--- | :--- |\n";

                foreach ($data['vendors'] ?? [] as $v) {
                    $out .= "| `{$v['code']}` | **{$v['name']}** | {$v['contact_person']} | {$v['phone']} | <span class=\"badge bg-info text-dark\">{$v['status']}</span> |\n";
                }
                return $out;

            case 'materials_stock':
                $count = $data['count'] ?? 0;
                $out = $isBangla
                    ? "### 🧱 সাইট নির্মাণ সামগ্রী ও স্টক রিপোর্ট\n\n" .
                      "| মালামালের নাম | প্রকল্প সাইট | বর্তমান মজুদ (Current Stock) | মোট গৃহীত | মোট ব্যবহার |\n" .
                      "| :--- | :--- | :--- | :--- | :--- |\n"
                    : "### 🧱 Site Material Inventory Stocks\n\n" .
                      "| Material Name | Project Site | Current Stock | Received | Consumed |\n" .
                      "| :--- | :--- | :--- | :--- | :--- |\n";

                foreach ($data['stocks'] ?? [] as $s) {
                    $out .= "| **{$s['material']}** | {$s['project']} | **{$s['current_qty']} {$s['unit']}** | {$s['total_received']} | {$s['total_consumed']} |\n";
                }
                return $out;

            case 'land_bank':
                $count = $data['total_parcels'] ?? 0;
                $totalKatha = $data['total_katha'] ?? 0;
                $out = $isBangla
                    ? "### 🗺️ কোম্পানির ল্যান্ড ব্যাংক ও জমির হিসাব\n\n" .
                      "> **সর্বমোট জমির পরিমাণ: {$totalKatha} কাঠা** (মোট পার্সেল: {$count} টি)\n\n" .
                      "| কোড | জমির শিরোনাম | মৌজা | পরিমাণ (কাঠা) | জেভি অনুপাত (Landowner/Dev) | অবস্থা |\n" .
                      "| :--- | :--- | :--- | :--- | :--- | :--- |\n"
                    : "### 🗺️ Land Bank & Parcel Assets\n\n" .
                      "> **Total Land Holding: {$totalKatha} Katha** (Total Parcels: {$count})\n\n" .
                      "| Code | Parcel Title | Mouza | Area (Katha) | JV Ratio | Status |\n" .
                      "| :--- | :--- | :--- | :--- | :--- | :--- |\n";

                foreach ($data['parcels'] ?? [] as $lp) {
                    $out .= "| `{$lp['code']}` | **{$lp['title']}** | {$lp['mouza']} | {$lp['katha']} | {$lp['ratio']} | <span class=\"badge bg-success\">{$lp['status']}</span> |\n";
                }
                return $out;

            case 'units_pricing':
                $high = $fmt($data['highest_price'] ?? 0);
                $low = $fmt($data['lowest_price'] ?? 0);
                $avgSqft = $fmt($data['avg_sqft_price'] ?? 0);

                $out = $isBangla
                    ? "### 💎 ফ্ল্যাট ও ইউনিট মূল্য তালিকা\n\n" .
                      "- **সর্বোচ্চ ইউনিট মূল্য:** **{$high}**\n" .
                      "- **সর্বনিম্ন ইউনিট মূল্য:** **{$low}**\n" .
                      "- **গড় প্রতি স্কয়ারফুট রেট:** **{$avgSqft}**\n\n" .
                      "| ইউনিট নম্বর | প্রজেক্ট | বেডরুম | সাইজ (sqft) | প্রতি স্কয়ারফুট | মোট মূল্য | অবস্থা |\n" .
                      "| :--- | :--- | :--- | :--- | :--- | :--- | :--- |\n"
                    : "### 💎 Unit Pricing & Apartment Register\n\n" .
                      "- **Highest Unit Price:** **{$high}**\n" .
                      "- **Lowest Unit Price:** **{$low}**\n" .
                      "- **Average Sqft Rate:** **{$avgSqft}**\n\n" .
                      "| Unit | Project | Beds | Size (sqft) | Base Rate | Total Price | Status |\n" .
                      "| :--- | :--- | :--- | :--- | :--- | :--- | :--- |\n";

                foreach ($data['units'] ?? [] as $u) {
                    $uPrice = $fmt($u['total_price']);
                    $uRate = $fmt($u['base_rate']);
                    $out .= "| **Unit {$u['unit_number']}** | {$u['project']} | {$u['bedrooms']} Bed | {$u['sqft']} | {$uRate} | **{$uPrice}** | <span class=\"badge bg-secondary\">{$u['status']}</span> |\n";
                }
                return $out;

            case 'transactions_info':
                $tInc = $fmt($data['today_income'] ?? 0);
                $tExp = $fmt($data['today_expense'] ?? 0);

                $out = $isBangla
                    ? "### 💳 সাম্প্রতিক লেনদেন ও ক্যাশ মুভমেন্ট\n\n" .
                      "- **আজকের মোট আয়:** **{$tInc}**\n" .
                      "- **আজকের মোট ব্যয়:** **{$tExp}**\n\n" .
                      "| ধরণ | বিবরণ | খাত / ক্যাটাগরি | পরিমাণ | মাধ্যম | তারিখ |\n" .
                      "| :--- | :--- | :--- | :--- | :--- | :--- |\n"
                    : "### 💳 Recent Cash & Bank Transactions\n\n" .
                      "- **Today's Inflow:** **{$tInc}**\n" .
                      "- **Today's Outflow:** **{$tExp}**\n\n" .
                      "| Type | Title | Category | Amount | Method | Date |\n" .
                      "| :--- | :--- | :--- | :--- | :--- | :--- |\n";

                foreach ($data['transactions'] ?? [] as $t) {
                    $tAmt = $fmt($t['amount']);
                    $badge = $t['type'] === 'income' ? '<span class="badge bg-success">Income</span>' : '<span class="badge bg-danger">Expense</span>';
                    $out .= "| {$badge} | {$t['title']} | {$t['category']} | **{$tAmt}** | {$t['method']} | {$t['date']} |\n";
                }
                return $out;

            case 'smart_entity_search':
                $term = $data['search_term'] ?? $query;
                $foundAny = false;
                $out = $isBangla
                    ? "### 🔍 '{$term}' এর অনুসন্ধান ফলাফল\n\n"
                    : "### 🔍 Search Results for '{$term}'\n\n";

                if (!empty($data['customers'])) {
                    $foundAny = true;
                    $out .= "#### 👤 সম্পর্কিত কাস্টমার:\n";
                    foreach ($data['customers'] as $c) {
                        $out .= "- **{$c['name']}** (Code: `{$c['code']}`), মোবাইল: `{$c['phone']}`, স্ট্যাটাস: `{$c['status']}`\n";
                    }
                    $out .= "\n";
                }

                if (!empty($data['leads'])) {
                    $foundAny = true;
                    $out .= "#### 🎯 সম্পর্কিত লিড:\n";
                    foreach ($data['leads'] as $l) {
                        $out .= "- **{$l['name']}** (Code: `{$l['code']}`), মোবাইল: `{$l['phone']}`, স্টেজ: `{$l['stage']}`\n";
                    }
                    $out .= "\n";
                }

                if (!empty($data['projects'])) {
                    $foundAny = true;
                    $out .= "#### 🏗️ সম্পর্কিত প্রজেক্ট:\n";
                    foreach ($data['projects'] as $p) {
                        $out .= "- **{$p['name']}** (Code: `{$p['code']}`), লোকেশন: `{$p['location']}`, স্ট্যাটাস: `{$p['status']}`\n";
                    }
                    $out .= "\n";
                }

                if (!empty($data['units'])) {
                    $foundAny = true;
                    $out .= "#### 🏢 সম্পর্কিত ফ্ল্যাট / ইউনিট:\n";
                    foreach ($data['units'] as $u) {
                        $out .= "- **Unit {$u['unit']}**, সাইজ: `{$u['sqft']} sqft`, মূল্য: **" . $fmt($u['price']) . "**, স্ট্যাটাস: `{$u['status']}`\n";
                    }
                    $out .= "\n";
                }

                if (!empty($data['employees'])) {
                    $foundAny = true;
                    $out .= "#### 👔 সম্পর্কিত কর্মকর্তা / কর্মচারী:\n";
                    foreach ($data['employees'] as $e) {
                        $out .= "- **{$e['name']}**, মোবাইল: `{$e['phone']}`, গ্রস বেতন: **" . $fmt($e['salary']) . "**\n";
                    }
                    $out .= "\n";
                }

                if (!$foundAny) {
                    $out = $isBangla
                        ? "### ℹ️ '{$term}' সম্পর্কিত কোনো তথ্য পাওয়া যায়নি।\n\n" .
                          "তবে আপনি নিচের তথ্যগুলো জানতে চাইতে পারেন:\n" .
                          "- **আজকের সেল কয়টা হয়েছে?**\n" .
                          "- **আজকের লাভ কত?**\n" .
                          "- **কত টাকা বকেয়া আছে?**\n" .
                          "- **প্রজেক্টের তালিকা দাও**\n" .
                          "- **কাস্টমারদের তালিকা দাও**"
                        : "### ℹ️ No specific record found for '{$term}'.\n\n" .
                          "You can ask about:\n" .
                          "- **Today's sales count**\n" .
                          "- **Today's net profit**\n" .
                          "- **Total pending dues**\n" .
                          "- **Project list or Customer directory**";
                }

                return $out;

            case 'sales_today':
                $count = $data['count'] ?? 0;
                $total = $fmt($data['total_contract_amount'] ?? 0);
                $down = $fmt($data['total_downpayment'] ?? 0);
                $dateStr = Carbon::parse($data['date'])->format('d M, Y');

                if ($count === 0) {
                    if ($isBangla) {
                        return "### 📊 আজকের সেল ও বুকিং রিপোর্ট ({$dateStr})\n\n" .
                               "> **আজকে নতুন কোনো সেল বা বুকিং রেকর্ড করা হয়নি (০ টি)।**\n\n" .
                               "- চলতি মাসে সর্বমোট সেল হয়েছে: **" . ($data['month_total_bookings'] ?? 0) . " টি**\n" .
                               "- নতুন বুকিং এন্ট্রি করতে [Sales Bookings Register](/erp/developer/bookings) ভিজিট করুন।";
                    }
                    return "### 📊 Today's Sales & Bookings Report ({$dateStr})\n\n" .
                           "> **No new sales bookings recorded today (0).**\n\n" .
                           "- Total bookings this month: **" . ($data['month_total_bookings'] ?? 0) . "**\n" .
                           "- Create a new booking via the [Sales Bookings Register](/erp/developer/bookings).";
                }

                $out = $isBangla
                    ? "### 🎯 আজকের সেল রিপোর্ট ({$dateStr})\n\n" .
                      "আজকে সর্বমোট **{$count} টি** সেল/বুকিং সফলভাবে সম্পন্ন হয়েছে।\n\n" .
                      "| মেট্রিক | পরিমাণ |\n" .
                      "| :--- | :--- |\n" .
                      "| **আজকের মোট সেল ভ্যালু** | **{$total}** |\n" .
                      "| **আজকের ডাউনপেমেন্ট সংগ্রহ** | **{$down}** |\n" .
                      "| **চলতি মাসের মোট সেল** | **" . ($data['month_total_bookings'] ?? 0) . " টি** |\n\n" .
                      "#### 📝 আজকের বুকিং এর বিবরণ:\n"
                    : "### 🎯 Today's Sales Report ({$dateStr})\n\n" .
                      "A total of **{$count} sales booking(s)** have been recorded today.\n\n" .
                      "| Metric | Value |\n" .
                      "| :--- | :--- |\n" .
                      "| **Total Contract Value** | **{$total}** |\n" .
                      "| **Total Downpayment Collected** | **{$down}** |\n" .
                      "| **Month-to-date Bookings** | **" . ($data['month_total_bookings'] ?? 0) . "** |\n\n" .
                      "#### 📝 Today's Bookings Breakdown:\n";

                $out .= "| কোড / Code | কাস্টমার / Customer | প্রজেক্ট ও ফ্ল্যাট | চুক্তি মূল্য | স্ট্যাটাস |\n";
                $out .= "| :--- | :--- | :--- | :--- | :--- |\n";
                foreach ($data['bookings'] as $b) {
                    $bAmt = $fmt($b['amount']);
                    $out .= "| `{$b['code']}` | {$b['customer']} | {$b['project']} (Unit {$b['unit']}) | {$bAmt} | <span class=\"badge bg-success\">{$b['status']}</span> |\n";
                }

                return $out;

            case 'sales_summary':
                $total = $data['total_count'] ?? 0;
                $month = $data['this_month_count'] ?? 0;
                $val = $fmt($data['total_contract_value'] ?? 0);

                $out = $isBangla
                    ? "### 🏢 বিক্রয় ও বুকিং সারসংক্ষেপ\n\n" .
                      "| মেট্রিক | পরিসংখ্যান |\n" .
                      "| :--- | :--- |\n" .
                      "| **সর্বমোট সেল/বুকিং সংখ্যা** | **{$total} টি** |\n" .
                      "| **চলতি মাসের সেল সংখ্যা** | **{$month} টি** |\n" .
                      "| **মোট বুকিং চুক্তি মূল্য** | **{$val}** |\n\n" .
                      "#### 🕒 সাম্প্রতিক সেল ও বুকিং তালিকা:\n"
                    : "### 🏢 Sales & Bookings Overall Summary\n\n" .
                      "| Metric | Figure |\n" .
                      "| :--- | :--- |\n" .
                      "| **Total Bookings Count** | **{$total}** |\n" .
                      "| **Current Month Bookings** | **{$month}** |\n" .
                      "| **Gross Contract Value** | **{$val}** |\n\n" .
                      "#### 🕒 Recent Bookings Register:\n";

                $out .= "| বুকিং কোড | কাস্টমার | প্রজেক্ট | ফ্ল্যাট | মূল্য | তারিখ |\n";
                $out .= "| :--- | :--- | :--- | :--- | :--- | :--- |\n";
                foreach ($data['recent_bookings'] ?? [] as $b) {
                    $bAmt = $fmt($b['amount']);
                    $out .= "| `{$b['code']}` | {$b['customer']} | {$b['project']} | Unit {$b['unit']} | {$bAmt} | {$b['date']} |\n";
                }
                return $out;

            case 'profit_today':
                $inc = $fmt($data['today_income'] ?? 0);
                $exp = $fmt($data['today_expense'] ?? 0);
                $labor = $fmt($data['today_labor_wage'] ?? 0);
                $totExp = $fmt($data['today_total_expense'] ?? 0);
                $net = $fmt($data['today_net_profit'] ?? 0);
                $mNet = $fmt($data['month_net_profit'] ?? 0);
                $isProfitable = ($data['today_net_profit'] ?? 0) >= 0;
                $statusBadge = $isProfitable ? "🟢 লাভ (Profit)" : "🔴 লোকসান (Deficit)";

                if ($isBangla) {
                    return "### 💰 আজকের লাভ ও আর্থিক হিসাব ({$data['date']})\n\n" .
                           "> **আজকের নেট লাভ: {$net}** ({$statusBadge})\n\n" .
                           "| হিসাবের খাত | আজকের পরিমাণ | চলতি মাসের মোট |\n" .
                           "| :--- | :--- | :--- |\n" .
                           "| 📥 **মোট ইনকাম / আয়** | **{$inc}** | " . $fmt($data['month_income'] ?? 0) . " |\n" .
                           "| 📤 **অপারেশনাল খরচ** | {$exp} | - |\n" .
                           "| 👷 **সাইট লেবার মজুরি** | {$labor} | - |\n" .
                           "| 🔴 **সর্বমোট খরচ / ব্যয়** | **{$totExp}** | " . $fmt($data['month_expense'] ?? 0) . " |\n" .
                           "| 💵 **নিট প্রফিট / ব্যালেন্স** | **{$net}** | **{$mNet}** |\n\n" .
                           "💡 *হিসাবটি আজকের ক্যাশ ও ব্যাংক ট্রানজেকশন এবং লেবার রোলের রিয়েল-টাইম ডাটা থেকে সমন্বয় করা হয়েছে।*";
                }

                return "### 💰 Today's Profit & Financial Performance ({$data['date']})\n\n" .
                       "> **Today's Net Profit: {$net}** ({$statusBadge})\n\n" .
                       "| Line Item | Today | This Month to Date |\n" .
                       "| :--- | :--- | :--- |\n" .
                       "| 📥 **Total Inflow / Income** | **{$inc}** | " . $fmt($data['month_income'] ?? 0) . " |\n" .
                       "| 📤 **Operational Expenses** | {$exp} | - |\n" .
                       "| 👷 **Site Labor Wages** | {$labor} | - |\n" .
                       "| 🔴 **Total Outflow** | **{$totExp}** | " . $fmt($data['month_expense'] ?? 0) . " |\n" .
                       "| 💵 **Net Profit / Balance** | **{$net}** | **{$mNet}** |\n\n" .
                       "💡 *Calculated automatically from real-time cash, bank, and daily labor muster roll journals.*";

            case 'profit_summary':
                $inc = $fmt($data['total_income'] ?? 0);
                $exp = $fmt($data['total_expense'] ?? 0);
                $labor = $fmt($data['total_labor_wages'] ?? 0);
                $payroll = $fmt($data['total_payroll'] ?? 0);
                $outflow = $fmt($data['total_outflow'] ?? 0);
                $net = $fmt($data['net_profit'] ?? 0);
                $margin = $data['margin_percent'] ?? 0;

                if ($isBangla) {
                    return "### 📈 সার্বিক লাভ ও আর্থিক বিবরণী (P&L Summary)\n\n" .
                           "> **কোম্পানির সর্বমোট নিট লাভ: {$net}** (মার্জিন: {$margin}%)\n\n" .
                           "| খাতের নাম | মোট পরিমাণ |\n" .
                           "| :--- | :--- |\n" .
                           "| 🟢 **মোট আয় (Revenue / Collections)** | **{$inc}** |\n" .
                           "| 🟠 সাধারণ ও প্রকল্প খরচ | {$exp} |\n" .
                           "| 👷 সাইট লেবার মজুরি | {$labor} |\n" .
                           "| 👔 স্টাফ বেতন ও পেরোল | {$payroll} |\n" .
                           "| 🔴 **মোট ব্যয় (Total Expenses)** | **{$outflow}** |\n" .
                           "| 💎 **নিট সঞ্চিতি / প্রফিট** | **{$net}** |\n\n" .
                           "- চলতি মাসে নতুন আয় হয়েছে: **" . $fmt($data['this_month_income'] ?? 0) . "**\n" .
                           "- চলতি মাসে মোট খরচ হয়েছে: **" . $fmt($data['this_month_expense'] ?? 0) . "**";
                }

                return "### 📈 Executive Profit & Loss Summary\n\n" .
                       "> **Net Operating Profit: {$net}** (Operating Margin: {$margin}%)\n\n" .
                       "| Category | Total Balance |\n" .
                       "| :--- | :--- |\n" .
                       "| 🟢 **Gross Income / Revenue** | **{$inc}** |\n" .
                       "| 🟠 General & Project Expenses | {$exp} |\n" .
                       "| 👷 Site Labor Muster Wages | {$labor} |\n" .
                       "| 👔 Permanent Staff Payroll | {$payroll} |\n" .
                       "| 🔴 **Total Outflows** | **{$outflow}** |\n" .
                       "| 💎 **Net Business Profit** | **{$net}** |\n\n" .
                       "- Current month income: **" . $fmt($data['this_month_income'] ?? 0) . "**\n" .
                       "- Current month expense: **" . $fmt($data['this_month_expense'] ?? 0) . "**";

            case 'dues':
                $totalDue = $fmt($data['total_due'] ?? 0);
                $overdue = $fmt($data['overdue_amount'] ?? 0);
                $upcoming = $fmt($data['upcoming_30_days_due'] ?? 0);
                $overdueCount = $data['overdue_count'] ?? 0;

                $out = $isBangla
                    ? "### ⚠️ কাস্টমার কিস্তি ও বকেয়া হিসাব (Due & Receivables)\n\n" .
                      "> **বর্তমানে সর্বমোট বকেয়া: {$totalDue}**\n\n" .
                      "| বকেয়ার ধরণ | মোট পরিমাণ | অবস্থা |\n" .
                      "| :--- | :--- | :--- |\n" .
                      "| 🚨 **মেয়াদোত্তীর্ণ বকেয়া (Overdue)** | **{$overdue}** | {$overdueCount} টি কিস্তির সময় পার হয়েছে |\n" .
                      "| ⏳ **আগামী ৩০ দিনের কিস্তি** | **{$upcoming}** | রিমাইন্ডার পাঠানোর জন্য উপযুক্ত |\n" .
                      "| 💰 **সর্বমোট আদায়যোগ্য ডিউ** | **{$totalDue}** | পেন্ডিং বা আংশিক পরিশোধিত |\n\n" .
                      "#### 📋 বকেয়া কিস্তির শীর্ষ তালিকা:\n"
                    : "### ⚠️ Customer Due Installments & Receivables\n\n" .
                      "> **Total Outstanding Receivables: {$totalDue}**\n\n" .
                      "| Due Category | Outstanding Amount | Status |\n" .
                      "| :--- | :--- | :--- |\n" .
                      "| 🚨 **Past Due / Overdue** | **{$overdue}** | {$overdueCount} installments overdue |\n" .
                      "| ⏳ **Upcoming (Next 30 Days)** | **{$upcoming}** | Scheduled for collection |\n" .
                      "| 💰 **Total Pending Balance** | **{$totalDue}** | Uncollected installments |\n\n" .
                      "#### 📋 Priority Overdue / Pending Installments:\n";

                $out .= "| কিস্তি / Milestone | কাস্টমার | মোবাইল | প্রজেক্ট ও ইউনিট | বকেয়ার পরিমাণ | শেষ তারিখ |\n";
                $out .= "| :--- | :--- | :--- | :--- | :--- | :--- |\n";
                foreach ($data['pending_installments'] ?? [] as $pi) {
                    $bDue = $fmt($pi['balance_due']);
                    $overdueTag = $pi['is_overdue'] ? '<span class="badge bg-danger">Overdue</span>' : '<span class="badge bg-warning text-dark">Upcoming</span>';
                    $out .= "| {$pi['milestone']} | {$pi['customer']} | {$pi['phone']} | {$pi['project']} ({$pi['unit']}) | **{$bDue}** | {$pi['due_date']} {$overdueTag} |\n";
                }
                return $out;

            case 'leads':
                $todayL = $data['today_leads'] ?? 0;
                $totL = $data['total_leads'] ?? 0;
                $stages = $data['by_stage'] ?? [];

                $stageText = [];
                foreach ($stages as $st => $cnt) {
                    $stageText[] = "**" . ucfirst($st) . "**: {$cnt} টি";
                }
                $stageSummary = implode(' | ', $stageText) ?: 'কোনো স্টেজ নেই';

                $out = $isBangla
                    ? "### 👥 সিআরএম লিড ও অনুসন্ধান পরিস্থিতি\n\n" .
                      "- **আজকের নতুন লিড:** **{$todayL} টি**\n" .
                      "- **সর্বমোট সক্রিয় ও নিবন্ধিত লিড:** **{$totL} টি**\n" .
                      "- **পাইপলাইন পর্যায়:** {$stageSummary}\n\n" .
                      "#### 📌 সাম্প্রতিক লিড তালিকা:\n"
                    : "### 👥 CRM Leads & Inquiries Pipeline\n\n" .
                      "- **New Leads Today:** **{$todayL}**\n" .
                      "- **Total Leads Recorded:** **{$totL}**\n" .
                      "- **Pipeline Stages:** {$stageSummary}\n\n" .
                      "#### 📌 Recent Lead Intake:\n";

                $out .= "| লিড আইডি | নাম | মোবাইল | সোর্স | স্টেজ | অগ্রাধিকার |\n";
                $out .= "| :--- | :--- | :--- | :--- | :--- | :--- |\n";
                foreach ($data['recent_leads'] ?? [] as $rl) {
                    $out .= "| `{$rl['code']}` | {$rl['name']} | {$rl['phone']} | {$rl['source']} | <span class=\"badge bg-info text-dark\">{$rl['stage']}</span> | {$rl['priority']} |\n";
                }
                return $out;

            case 'inventory':
                $tot = $data['total_units'] ?? 0;
                $avail = $data['available_units'] ?? 0;
                $book = $data['booked_units'] ?? 0;
                $sold = $data['sold_units'] ?? 0;

                $out = $isBangla
                    ? "### 🏢 অ্যাপার্টমেন্ট ও ইউনিট ইনভেন্টরি স্টক\n\n" .
                      "| অবস্থা | ইউনিটের সংখ্যা | শতকরা হার |\n" .
                      "| :--- | :--- | :--- |\n" .
                      "| 🟢 **বিক্রয়ের জন্য খালি (Available)** | **{$avail} টি** | " . ($tot > 0 ? round(($avail / $tot) * 100, 1) : 0) . "% |\n" .
                      "| 🟡 **বুকিং সম্পন্ন (Booked)** | **{$book} টি** | " . ($tot > 0 ? round(($book / $tot) * 100, 1) : 0) . "% |\n" .
                      "| 🔴 **সম্পূর্ণ বিক্রয়কৃত (Sold)** | **{$sold} টি** | " . ($tot > 0 ? round(($sold / $tot) * 100, 1) : 0) . "% |\n" .
                      "| 🏢 **সর্বমোট ফ্ল্যাট/ইউনিট সংখ্যা** | **{$tot} টি** | 100% |\n\n" .
                      "#### 🏗️ প্রজেক্টভিত্তিক ইউনিট হিসাব:\n"
                    : "### 🏢 Project Unit & Flat Inventory\n\n" .
                      "| Unit Status | Count | Share |\n" .
                      "| :--- | :--- | :--- |\n" .
                      "| 🟢 **Available for Sale** | **{$avail}** | " . ($tot > 0 ? round(($avail / $tot) * 100, 1) : 0) . "% |\n" .
                      "| 🟡 **Under Booking Contract** | **{$book}** | " . ($tot > 0 ? round(($book / $tot) * 100, 1) : 0) . "% |\n" .
                      "| 🔴 **Fully Sold / Handed Over** | **{$sold}** | " . ($tot > 0 ? round(($sold / $tot) * 100, 1) : 0) . "% |\n" .
                      "| 🏢 **Total Project Units** | **{$tot}** | 100% |\n\n" .
                      "#### 🏗️ Project-Wise Breakdown:\n";

                $out .= "| প্রজেক্টের নাম | মোট ইউনিট | খালি (Available) | বুকড | সোল্ড |\n";
                $out .= "| :--- | :--- | :--- | :--- | :--- |\n";
                foreach ($data['projects'] ?? [] as $pj) {
                    $out .= "| {$pj['project']} | **{$pj['total']}** | <span class=\"badge bg-success\">{$pj['available']}</span> | {$pj['booked']} | {$pj['sold']} |\n";
                }
                return $out;

            case 'labor':
                $tCount = $data['today_count'] ?? 0;
                $tWage = $fmt($data['today_wages'] ?? 0);
                $lDate = $data['latest_date'] ? Carbon::parse($data['latest_date'])->format('d M, Y') : 'N/A';
                $lCount = $data['latest_count'] ?? 0;
                $lWage = $fmt($data['latest_wages'] ?? 0);
                $allWage = $fmt($data['total_all_time_wages'] ?? 0);

                if ($isBangla) {
                    return "### 👷 সাইট নির্মাণ শ্রমিক ও দৈনিক মজুরি রিপোর্ট\n\n" .
                           "- **আজকের মোট হাজিরা:** **{$tCount} জন**\n" .
                           "- **আজকের মোট প্রদেয় মজুরি:** **{$tWage}**\n\n" .
                           "| খাত | বিবরণ |\n" .
                           "| :--- | :--- |\n" .
                           "| 📅 **সর্বশেষ মাস্টার রোল তারিখ** | {$lDate} |\n" .
                           "| 👥 **সর্বশেষ দিনের শ্রমিক সংখ্যা** | {$lCount} জন |\n" .
                           "| 💵 **সর্বশেষ দিনের মোট মজুরি বিল** | **{$lWage}** |\n" .
                           "| 🏗️ **সর্বমোট পরিশোধিত/প্রদেয় মজুরি** | **{$allWage}** |\n\n" .
                           "নতুন হাজিরা বা মজুরি এন্ট্রি করতে [Daily Labor Muster](/erp/hr/labor/muster) পেইজ ব্যবহার করুন।";
                }

                return "### 👷 Site Construction Labor Muster & Wages\n\n" .
                       "- **Present Today:** **{$tCount} laborers**\n" .
                       "- **Payable Wages Today:** **{$tWage}**\n\n" .
                       "| Line Item | Value |\n" .
                       "| :--- | :--- |\n" .
                       "| 📅 **Latest Muster Roll Date** | {$lDate} |\n" .
                       "| 👥 **Laborers on That Date** | {$lCount} workers |\n" .
                       "| 💵 **Daily Wage Total** | **{$lWage}** |\n" .
                       "| 🏗️ **Total Labor Wages Cumulative** | **{$allWage}** |\n\n" .
                       "To record today's attendance, navigate to [Daily Labor Muster](/erp/hr/labor/muster).";

            case 'employees':
                $emp = $data['active_employees'] ?? 0;
                $paid = $fmt($data['total_payroll_paid'] ?? 0);
                $pend = $fmt($data['pending_payroll_approval'] ?? 0);

                $out = $isBangla
                    ? "### 👔 কোম্পানির স্থায়ী কর্মকর্তা ও স্টাফ পেরোল\n\n" .
                      "- **সক্রিয় কর্মকর্তা ও কর্মচারীর সংখ্যা:** **{$emp} জন**\n" .
                      "- **পরিশোধিত সর্বমোট পেরোল স্যালারি:** **{$paid}**\n" .
                      "- **অনুমোদনের অপেক্ষায় থাকা বেতন বিল:** **{$pend}**\n\n" .
                      "#### 📋 কর্মকর্তা ও কর্মচারীদের তালিকা:\n"
                    : "### 👔 Permanent Staff & Payroll Overview\n\n" .
                      "- **Active Permanent Employees:** **{$emp}**\n" .
                      "- **Total Net Salaries Disbursed:** **{$paid}**\n" .
                      "- **Pending Payroll Approvals:** **{$pend}**\n\n" .
                      "#### 📋 Staff Directory:\n";

                $out .= "| আইডি | নাম | পদবী | বিভাগ | গ্রস বেতন | মোবাইল |\n";
                $out .= "| :--- | :--- | :--- | :--- | :--- | :--- |\n";
                foreach ($data['employees'] ?? [] as $e) {
                    $gSal = $fmt($e['gross_salary']);
                    $out .= "| `{$e['code']}` | **{$e['name']}** | {$e['designation']} | {$e['department']} | {$gSal} | {$e['phone']} |\n";
                }
                return $out;

            case 'help':
                if ($isBangla) {
                    $out = "### 💡 আমি যেভাবে আপনাকে সাহায্য করতে পারি:\n\n" .
                           "আমি রিয়েল এস্টেট ইআরপি এবং সিএমএস ডাটাবেসের সাথে সংযুক্ত একটি কৃত্রিম বুদ্ধিমত্তা। আপনি যেকোনো প্রশ্ন করতে পারেন:\n\n";
                    foreach ($data['supported_topics'] as $topic => $samples) {
                        $out .= "#### 🔹 {$topic}\n";
                        foreach ($samples as $s) {
                            $out .= "- `{$s}`\n";
                        }
                        $out .= "\n";
                    }
                    return $out;
                }

                $out = "### 💡 What I Can Help You With:\n\n" .
                       "I am connected directly to your Real Estate ERP & CMS database. Ask me any question:\n\n";
                foreach ($data['supported_topics'] as $topic => $samples) {
                    $out .= "#### 🔹 {$topic}\n";
                    foreach ($samples as $s) {
                        $out .= "- `{$s}`\n";
                    }
                    $out .= "\n";
                }
                return $out;

            case 'company_overview':
            default:
                $dateStr = Carbon::parse($data['date'] ?? today())->format('d M, Y');
                $tSales = $data['today_sales_count'] ?? 0;
                $tSalesVal = $fmt($data['today_sales_value'] ?? 0);
                $tInc = $fmt($data['today_income'] ?? 0);
                $tExp = $fmt($data['today_expense'] ?? 0);
                $tProfit = $fmt($data['today_profit'] ?? 0);
                $dues = $fmt($data['total_dues'] ?? 0);
                $overdue = $fmt($data['overdue_dues'] ?? 0);
                $avail = $data['available_units'] ?? 0;
                $totUnits = $data['total_units'] ?? 0;
                $todayL = $data['today_leads'] ?? 0;
                $totL = $data['total_leads'] ?? 0;
                $totPrj = $data['total_projects'] ?? 0;
                $totCust = $data['total_customers'] ?? 0;

                if ($isBangla) {
                    return "### 🏢 কোম্পানির সার্বিক দৈনিক এক্সিকিউটিভ রিপোর্ট ({$dateStr})\n\n" .
                           "| ক্ষেত্র | আজকের ডাটা | সামগ্রিক ডাটাবেস অবস্থা |\n" .
                           "| :--- | :--- | :--- |\n" .
                           "| 🎯 **বিক্রয় ও বুকিং** | **{$tSales} টি** ({$tSalesVal}) | বুকিং কার্যক্রম চলমান |\n" .
                           "| 💰 **আজকের নেট প্রফিট** | **{$tProfit}** | আয়: {$tInc} / ব্যয়: {$tExp} |\n" .
                           "| ⚠️ **বকেয়া কিস্তি (Dues)** | ওভারডিউ: **{$overdue}** | সর্বমোট বকেয়া: **{$dues}** |\n" .
                           "| 👥 **সিআরএম লিডস** | নতুন লিড: **{$todayL} টি** | মোট লিড: **{$totL} টি** |\n" .
                           "| 🏢 **ফ্ল্যাট ও ইউনিট স্টক** | অবিক্রিত: **{$avail} টি** | মোট ফ্ল্যাট: **{$totUnits} টি** |\n" .
                           "| 🏗️ **প্রকল্প ও গ্রাহক** | মোট প্রজেক্ট: **{$totPrj} টি** | মোট নিবন্ধিত কাস্টমার: **{$totCust} জন** |\n\n" .
                           "💡 *নির্দিষ্ট কোনো তথ্য জানতে সরাসরি নাম, কোড বা প্রশ্ন লিখুন (যেমন: \"প্রজেক্ট লিস্ট\", \"কাস্টমারদের নাম\", \"আজকের সেল\")।*";
                }

                return "### 🏢 Enterprise Executive Snapshot ({$dateStr})\n\n" .
                       "| Department / Metric | Today's Activity | Aggregate Status |\n" .
                       "| :--- | :--- | :--- |\n" .
                       "| 🎯 **Sales Bookings** | **{$tSales} booking(s)** ({$tSalesVal}) | In progress |\n" .
                       "| 💰 **Today's Net Profit** | **{$tProfit}** | Inflow: {$tInc} / Outflow: {$tExp} |\n" .
                       "| ⚠️ **Customer Dues** | Overdue: **{$overdue}** | Total Due: **{$dues}** |\n" .
                       "| 👥 **CRM Leads** | New today: **{$todayL}** | Total database: **{$totL}** |\n" .
                       "| 🏢 **Unit Inventory** | Available: **{$avail} units** | Total Units: **{$totUnits}** |\n" .
                       "| 🏗️ **Projects & Customers** | Active Projects: **{$totPrj}** | Total Customers: **{$totCust}** |\n\n" .
                       "💡 *Ask specific questions anytime (e.g. \"project list\", \"customer list\", \"how much due pending\").*";
        }
    }

    /**
     * Optional Gemini API integration for flexible conversational synthesis.
     */
    protected function askGemini(string $query, string $intent, array $data, string $apiKey, bool $isBangla): ?string
    {
        try {
            $systemInstruction = "You are an intelligent AI Database Assistant for Aurelia Real Estate ERP & CMS. " .
                "Today's date is " . Carbon::today()->format('Y-m-d') . ". " .
                "You must strictly base your answers on the provided real-time database facts JSON. " .
                "Never invent financial numbers. If the user asks in Bengali or Banglish, answer in clear, polite Bengali. " .
                "Format figures with 'BDT' and use Markdown tables and bold highlights.";

            $prompt = "Database Facts for Intent '{$intent}':\n" . json_encode($data, JSON_PRETTY_PRINT) . "\n\n" .
                      "User Question: \"{$query}\"\n\n" .
                      "Please provide an accurate, polite, and well-structured answer:";

            $response = Http::timeout(6)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $systemInstruction . "\n\n" . $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'maxOutputTokens' => 1000,
                ],
            ]);

            if ($response->successful()) {
                $body = $response->json();
                return $body['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }
        } catch (\Throwable $e) {
            Log::warning('Gemini API call skipped: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Default suggestion queries.
     */
    public function getDefaultSuggestions(): array
    {
        return [
            'আজকের সেল কয়টা হয়েছে?',
            'আজকের লাভ কত?',
            'কত টাকা বকেয়া আছে?',
            'আমাদের প্রজেক্টের তালিকা দাও',
            'কাস্টমারদের তালিকা দেখাও',
            'খালি ইউনিট কয়টা বাকি আছে?',
            'সব ভেন্ডরের তালিকা',
            'আজকের সাইট লেবার কয়জন?',
        ];
    }

    /**
     * Suggestions tailored to current intent.
     */
    protected function getRelevantSuggestions(string $intent): array
    {
        switch ($intent) {
            case 'search_code':
            case 'smart_entity_search':
                return [
                    'আমাদের প্রজেক্টের তালিকা দাও',
                    'আজকের সেল কয়টা হয়েছে?',
                    'কত টাকা বকেয়া আছে?',
                ];
            case 'projects_info':
                return [
                    'খালি ইউনিট কয়টা বাকি আছে?',
                    'সবচেয়ে দামি ফ্ল্যাট কোনটা?',
                    'আজকের সেল কয়টা হয়েছে?',
                ];
            case 'customers_info':
                return [
                    'কত টাকা বকেয়া আছে?',
                    'কার কার কিস্তি বকেয়া আছে?',
                    'আজকের সেল কয়টা হয়েছে?',
                ];
            case 'vendors_info':
            case 'materials_stock':
                return [
                    'সাইট নির্মাণ সামগ্রীর স্টক কত?',
                    'আজকের লাভ কত?',
                    'আমাদের প্রজেক্টের তালিকা দাও',
                ];
            case 'sales_today':
                return [
                    'চলতি মাসের মোট সেল কত?',
                    'কত টাকা বকেয়া আছে?',
                    'আজকের লাভ কত?',
                ];
            case 'profit_today':
                return [
                    'চলতি মাসের মোট লাভ কত?',
                    'আজকের সেল কয়টা হয়েছে?',
                    'কত টাকা বকেয়া আছে?',
                ];
            case 'dues':
                return [
                    'কার কার কিস্তি বকেয়া আছে?',
                    'আজকের লাভ কত?',
                    'আজকের সেল কয়টা হয়েছে?',
                ];
            case 'leads':
                return [
                    'ইউনিট কয়টা বাকি আছে?',
                    'আজকের সেল কয়টা হয়েছে?',
                    'সার্বিক অবস্থা কেমন?',
                ];
            default:
                return $this->getDefaultSuggestions();
        }
    }
}
