<script lang="ts">
  import { cn } from '@/lib/utils.js';
  import { MoreHorizontalIcon } from 'lucide-svelte';
  import type { HTMLAttributes } from 'svelte/elements';

  let { class: className, ...props }: HTMLAttributes<HTMLSpanElement> =
    $props();
</script>

<span
  aria-hidden={true}
  data-slot="pagination-ellipsis"
  class={cn(
    "flex size-8 items-center justify-center [&_svg:not([class*='size-'])]:size-4",
    className,
  )}
  {...props}
>
  <MoreHorizontalIcon />
  <span class="sr-only">More pages</span>
</span>
