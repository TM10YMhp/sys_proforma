<script lang="ts">
  import { cn } from '@/lib/utils.js';
  import type { HTMLAttributes } from 'svelte/elements';

  let {
    class: className,
    children,
    ...props
  }: HTMLAttributes<HTMLUListElement> = $props();
</script>

<ul
  data-slot="pagination-content"
  class={cn('flex items-center gap-0.5', className)}
  {...props}
>
  {@render children?.()}
</ul>
