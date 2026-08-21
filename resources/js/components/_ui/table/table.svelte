<script lang="ts">
  import { cn } from '@/lib/utils.js';
  import type { HTMLTableAttributes } from 'svelte/elements';

  let { class: className, children, ...props }: HTMLTableAttributes = $props();
</script>

<div data-slot="table-container" class="relative w-full overflow-x-auto">
  <table
    data-slot="table"
    class={cn('w-full caption-bottom text-sm', className)}
    {...props}
  >
    {@render children?.()}
  </table>
</div>
