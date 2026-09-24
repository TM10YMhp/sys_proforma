<script lang="ts">
  import { cn } from '@/lib/utils.js';
  import type { HTMLAttributes } from 'svelte/elements';

  let {
    class: className,
    children,
    ...props
  }: HTMLAttributes<HTMLTableSectionElement> = $props();
</script>

<thead
  data-slot="table-header"
  class={cn('[&_tr]:border-b', className)}
  {...props}
>
  {@render children?.()}
</thead>
