<script lang="ts">
  import { Link } from '@inertiajs/svelte';
  import type { ComponentProps } from 'svelte';
  import { cn } from '@/lib/utils';

  type Props = ComponentProps<typeof Link> & {
    isActive?: boolean;
  };
  let { isActive, class: className, children, ...props }: Props = $props();
</script>

<Link
  aria-current={isActive ? 'page' : undefined}
  data-slot="pagination-link"
  data-active={isActive}
  class={cn(
    className,
    isActive
      ? 'hover:bg-primary-100 hover:text-primary-700 border-gray-700 bg-gray-700 text-white'
      : 'bg-gray-800 border-gray-700 text-gray-400 hover:bg-gray-700 hover:text-white',
  )}
  {...props}
>
  {@render children?.()}
</Link>
