export interface Contact {
    id: number;
    name: string;
    phone: string | null;
    email: string | null;
    source: 'sreality' | 'doporuceni' | 'fermakleri' | 'socialni_site' | 'vlastni_web' | 'jiny';
    type: 'kupec' | 'prodavajici' | 'najemnik' | 'investor';
    status: 'aktivni' | 'ceka' | 'uzavreno' | 'archiv';
    tag: 'horka' | 'ok' | 'studena' | 'uzavreno' | null;
    process_id: number | null;
    current_step: number | null;
    notes: string | null;
    created_at: string;
    updated_at: string;
    process?: Process;
    tasks?: Task[];
    activity_logs?: ActivityLog[];
    progress?: number | null;
}

export interface Process {
    id: number;
    name: string;
    color: string;
    badge: string | null;
    note: string | null;
    created_at: string;
    updated_at: string;
    steps?: Step[];
    contacts?: Contact[];
    contacts_count?: number;
}

export interface Step {
    id: number;
    process_id: number;
    order: number;
    name: string;
    description: string | null;
    duration_days: number;
    is_auto: boolean;
    created_at: string;
    updated_at: string;
}

export interface Task {
    id: number;
    contact_id: number;
    step_id: number | null;
    title: string;
    due_date: string;
    priority: 'vysoka' | 'stredni' | 'nizka';
    is_done: boolean;
    is_auto: boolean;
    created_at: string;
    updated_at: string;
    contact?: Contact;
    step?: Step;
}

export interface ActivityLog {
    id: number;
    contact_id: number;
    type: string;
    description: string;
    created_at: string;
    updated_at: string;
    contact?: Contact;
}

export interface ContractTemplate {
    id: number;
    name: string;
    file_path: string;
    variables: string[] | null;
    created_at: string;
    updated_at: string;
}

export interface Contract {
    id: number;
    template_id: number;
    contact_id: number;
    data: Record<string, string> | null;
    status: 'koncept' | 'validace' | 'zvalidovano' | 'odeslano' | 'podepsano' | 'zamitnuto';
    validation_result: string | null;
    verification_result: Record<string, any> | null;
    created_at: string;
    updated_at: string;
    template?: ContractTemplate;
    contact?: Contact;
}

export interface PriceAnalysis {
    id: number;
    contact_id: number | null;
    address: string;
    area: number;
    property_type: string;
    condition: string;
    floor: number | null;
    ownership: string | null;
    estimated_price: number | null;
    comparables: any[] | null;
    report_url: string | null;
    created_at: string;
    updated_at: string;
    contact?: Contact;
}

export interface NotificationTemplate {
    id: number;
    name: string;
    type: 'sms' | 'email';
    subject: string | null;
    body: string;
    created_at: string;
    updated_at: string;
}

export interface PaginatedData<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

export interface FlashMessages {
    success?: string;
    error?: string;
    warning?: string;
    info?: string;
}

export const SOURCE_LABELS: Record<Contact['source'], string> = {
    sreality: 'Sreality',
    doporuceni: 'Doporučení',
    fermakleri: 'Férmakléři',
    socialni_site: 'Sociální sítě',
    vlastni_web: 'Vlastní web',
    jiny: 'Jiný',
};

export const TYPE_LABELS: Record<Contact['type'], string> = {
    kupec: 'Kupec',
    prodavajici: 'Prodávající',
    najemnik: 'Nájemník',
    investor: 'Investor',
};

export const STATUS_LABELS: Record<Contact['status'], string> = {
    aktivni: 'Aktivní',
    ceka: 'Čeká',
    uzavreno: 'Uzavřeno',
    archiv: 'Archiv',
};

export const TAG_LABELS: Record<string, string> = {
    horka: 'Horká',
    ok: 'OK',
    studena: 'Studená',
    uzavreno: 'Uzavřeno',
};

export const TAG_COLORS: Record<string, string> = {
    horka: 'red',
    ok: 'blue',
    studena: 'gray',
    uzavreno: 'green',
};

export const PRIORITY_LABELS: Record<Task['priority'], string> = {
    vysoka: 'Vysoká',
    stredni: 'Střední',
    nizka: 'Nízká',
};

export const PRIORITY_COLORS: Record<Task['priority'], string> = {
    vysoka: 'red',
    stredni: 'yellow',
    nizka: 'gray',
};

export interface Property {
    id: number;
    name: string;
    address: string;
    city: string | null;
    zip: string | null;
    gps_lat: number | null;
    gps_lng: number | null;
    property_type: 'byt' | 'dum' | 'pozemek' | 'komercni';
    disposition: string | null;
    area: number | null;
    land_area: number | null;
    price: number | null;
    price_type: 'prodej' | 'pronajem';
    commission_percent: number | null;
    commission_amount: number | null;
    ad_budget: number | null;
    ad_spent: number | null;
    description: string | null;
    status: 'nabor' | 'priprava' | 'inzerce' | 'prohlidky' | 'rezervace' | 'smlouva' | 'prodano' | 'archiv';
    contact_id: number | null;
    price_analysis_id: number | null;
    notes: string | null;
    published_at: string | null;
    sold_at: string | null;
    created_at: string;
    updated_at: string;
    contact?: Pick<Contact, 'id' | 'name' | 'phone'>;
    price_analysis?: PriceAnalysis;
    photos?: PropertyPhoto[];
    interests?: PropertyInterest[];
    events?: PropertyEvent[];
    progress?: number;
    days_on_market?: number | null;
    primary_photo?: PropertyPhoto | null;
    commission_computed?: number | null;
    ad_remaining?: number | null;
}

export interface PropertyPhoto {
    id: number;
    property_id: number;
    file_path: string;
    caption: string | null;
    order: number;
    is_primary: boolean;
    url?: string;
}

export interface PropertyInterest {
    id: number;
    property_id: number;
    contact_id: number;
    type: 'zajemce' | 'navsteva' | 'rezervace';
    note: string | null;
    visited_at: string | null;
    created_at: string;
    contact?: Pick<Contact, 'id' | 'name' | 'phone' | 'email'>;
}

export interface PropertyEvent {
    id: number;
    property_id: number;
    contact_id: number | null;
    title: string;
    description: string | null;
    type: 'prohlidka' | 'schuzka' | 'foceni' | 'jine';
    starts_at: string;
    ends_at: string | null;
    location: string | null;
    is_completed: boolean;
    notes: string | null;
    created_at: string;
    contact?: Pick<Contact, 'id' | 'name'>;
}

export const PROPERTY_TYPE_LABELS: Record<Property['property_type'], string> = {
    byt: 'Byt',
    dum: 'Dům',
    pozemek: 'Pozemek',
    komercni: 'Komerční',
};

export const PROPERTY_STATUS_LABELS: Record<Property['status'], string> = {
    nabor: 'Nábor',
    priprava: 'Příprava',
    inzerce: 'Inzerce',
    prohlidky: 'Prohlídky',
    rezervace: 'Rezervace',
    smlouva: 'Smlouva',
    prodano: 'Prodáno',
    archiv: 'Archiv',
};

export const PROPERTY_STATUS_COLORS: Record<Property['status'], string> = {
    nabor: 'gray',
    priprava: 'blue',
    inzerce: 'green',
    prohlidky: 'yellow',
    rezervace: 'purple',
    smlouva: 'indigo',
    prodano: 'gold',
    archiv: 'gray',
};

export const PROPERTY_PRICE_TYPE_LABELS: Record<Property['price_type'], string> = {
    prodej: 'Prodej',
    pronajem: 'Pronájem',
};

export const INTEREST_TYPE_LABELS: Record<PropertyInterest['type'], string> = {
    zajemce: 'Zájemce',
    navsteva: 'Návštěva',
    rezervace: 'Rezervace',
};

export const INTEREST_TYPE_COLORS: Record<PropertyInterest['type'], string> = {
    zajemce: 'blue',
    navsteva: 'yellow',
    rezervace: 'green',
};

export const EVENT_TYPE_LABELS: Record<PropertyEvent['type'], string> = {
    prohlidka: 'Prohlídka',
    schuzka: 'Schůzka',
    foceni: 'Focení',
    jine: 'Jiné',
};
