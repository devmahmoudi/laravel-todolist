import { LucideIcon } from 'lucide-react';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

export interface Group {
    id: number;
    name: string;
    owner_id: number;
    owner?: User;
    todos?: Todo[];
    created_at: string;
    updated_at: string;
}

export interface Todo {
    id: number;
    title: string;
    description: string | null;
    parent_id: number | null;
    group_id: number | null;
    group?: Group;
    parent?: Todo;
    children?: Todo[];
    created_at: string;
    updated_at: string;
}
