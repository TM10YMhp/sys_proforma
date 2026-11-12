import type { LinkComponentBaseProps } from '@inertiajs/core';
import { clsx } from 'clsx';
import type { ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export function toUrl(
  href: NonNullable<LinkComponentBaseProps['href']>,
): string {
  return typeof href === 'string' ? href : href.url;
}

// TODO: deberia ocurrir en el backend pero falta tiempo para
// unificar las interfaces
// NOTE: elimina la pagina 1 de la url para usar la cache de inertia
export function cleanPageUrl(url: string | null) {
  if (!url) {
    return;
  }

  const parsedUrl = new URL(url, window.location.origin);

  if (parsedUrl.searchParams.get('page') === '1') {
    parsedUrl.searchParams.delete('page');
  }

  return parsedUrl.pathname + parsedUrl.search;
}
