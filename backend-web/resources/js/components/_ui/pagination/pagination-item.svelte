<script lang="ts">
  import type { HTMLLiAttributes } from 'svelte/elements';

  let { children, ...props }: HTMLLiAttributes = $props();
</script>

<li data-slot="pagination-item" {...props}>
  {@render children?.()}
</li>
