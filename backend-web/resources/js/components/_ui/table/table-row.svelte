<script lang="ts">
  import { cn } from '@/lib/utils.js';
  import type { HTMLAttributes } from 'svelte/elements';

  let {
    class: className,
    children,
    ...props
  }: HTMLAttributes<HTMLTableRowElement> = $props();
</script>

<tr
  data-slot="table-row"
  class={cn(
    'border-b transition-colors hover:bg-muted/50 has-aria-expanded:bg-muted/50 data-[state=selected]:bg-muted',
    className,
  )}
  {...props}
>
  {@render children?.()}
</tr>
