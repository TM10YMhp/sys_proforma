<script lang="ts">
  import { cn } from '@/lib/utils.js';
  import type { HTMLAttributes } from 'svelte/elements';

  let {
    class: className,
    children,
    ...props
  }: HTMLAttributes<HTMLElement> = $props();
</script>

<nav
  aria-label="pagination"
  data-slot="pagination"
  class={cn('mx-auto flex w-full justify-center', className)}
  {...props}
>
  {@render children?.()}
</nav>
