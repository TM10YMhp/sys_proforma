<script lang="ts">
  import { cn } from '@/lib/utils.js';
  import type { HTMLAnchorAttributes } from 'svelte/elements';
  import PaginationLink from './pagination-link.svelte';
  import { ChevronLeftIcon } from 'lucide-svelte';

  let {
    class: className,
    text,
    ...props
  }: HTMLAnchorAttributes & { text?: string } = $props();
</script>

<PaginationLink
  aria-label="Go to previous page"
  class={cn('pl-1.5!', className)}
  {...props}
>
  <ChevronLeftIcon data-icon="inline-start" className="cn-rtl-flip" />
  <span class="hidden sm:block">{text}</span>
</PaginationLink>
