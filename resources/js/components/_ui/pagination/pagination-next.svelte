<script lang="ts">
  import { cn } from '@/lib/utils.js';
  import type { HTMLAnchorAttributes } from 'svelte/elements';
  import PaginationLink from './pagination-link.svelte';
  import { ChevronRightIcon } from 'lucide-svelte';

  let {
    class: className,
    text,
    ...props
  }: HTMLAnchorAttributes & {
    text?: string;
  } = $props();
</script>

<PaginationLink
  aria-label="Go to next page"
  class={cn('pr-1.5!', className)}
  {...props}
>
  <span class="hidden sm:block">{text}</span>
  <ChevronRightIcon data-icon="inline-end" className="cn-rtl-flip" />
</PaginationLink>
