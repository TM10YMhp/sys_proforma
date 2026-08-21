<script lang="ts">
  import { cn } from '@/lib/utils.js';
  import type { HTMLAttributes } from 'svelte/elements';

  let {
    class: className,
    children,
    ...props
  }: HTMLAttributes<HTMLElement> = $props();
</script>

<caption
  data-slot="table-caption"
  class={cn('mt-4 text-sm text-muted-foreground', className)}
  {...props}
>
  {@render children?.()}
</caption>
