import { type ClassValue, clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';
import { format as fnsFormat } from 'date-fns'

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function dateFnsFormat(date: Date | string, format: string) {
    if (typeof date == 'string')
        date = new Date(date)

    return fnsFormat(date, format)
}

export function preventNavigate(e: MouseEvent | KeyboardEvent) {
    e.stopPropagation();
    e.preventDefault();
}

export function removeHtmlBodyNonePointerEventsStyle(){
    const body = document.getElementsByTagName('body')[0]
    body.style.pointerEvents = ''
}
