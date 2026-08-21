<script lang="ts">
  import { cn } from '@/lib/utils.js';
  import type { HTMLTdAttributes } from 'svelte/elements';

  let { class: className, children, ...props }: HTMLTdAttributes = $props();
</script>

<td
  data-slot="table-cell"
  class={cn(
    'p-2 align-middle whitespace-nowrap has-[[role=checkbox]]:pr-0',
    className,
  )}
  {...props}
>
  {@render children?.()}
</td>
