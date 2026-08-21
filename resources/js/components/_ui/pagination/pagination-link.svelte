<!-- TODO: check this -->
<script lang="ts">
  import { cn } from '@/lib/utils.js';
  import type { HTMLAnchorAttributes } from 'svelte/elements';
  let {
    // class: className,
    isActive,
    children,
    ...props
  }: HTMLAnchorAttributes & {
    // TODO: los parametros no estan pensados correctament
    isActive?: boolean;
  } = $props();
</script>

<a
  aria-current={isActive ? 'page' : undefined}
  data-slot="pagination-link"
  data-active={isActive}
  {...props}
>
  {@render children?.()}
</a>
