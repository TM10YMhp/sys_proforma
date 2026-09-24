<script lang="ts">
  import { cn } from '@/lib/utils.js';
  import type { HTMLThAttributes } from 'svelte/elements';

  let { class: className, children, ...props }: HTMLThAttributes = $props();
</script>

<th
  data-slot="table-head"
  class={cn(
    'h-10 px-2 text-left align-middle font-medium whitespace-nowrap text-foreground has-[[role=checkbox]]:pr-0',
    className,
  )}
  {...props}
>
  {@render children?.()}
</th>
