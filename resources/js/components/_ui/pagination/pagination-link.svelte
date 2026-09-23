<script lang="ts">
  import type { HTMLAnchorAttributes } from 'svelte/elements';
  import { cn } from '@/lib/utils';

  type Props = HTMLAnchorAttributes & {
    isActive?: boolean;
  };
  let { isActive, class: className, children, ...props }: Props = $props();
</script>

<a
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
</a>

<!-- TODO:
- funciona pero afecta a otros componentes
- los tipos no se infieren correctamente desde afuera
-->
<!-- NOTE:
- svelte pasa por compilacion, quizas eso afecta la deteccion de tipos
- es mas sencillo crear componentes de responsabilidad unica
-->
<!-- <script lang="ts" generics="T extends string | Component<any> = 'a'">
  import type { Component, ComponentProps, Snippet } from 'svelte';
  import type { HTMLAttributes } from 'svelte/elements';

  type HTMLTagProps<E> = E extends keyof HTMLElementTagNameMap
    ? HTMLAttributes<HTMLElementTagNameMap[E]>
    : Record<string, any>;

  type Props = {
    isActive?: boolean;
    as: T;
    children?: Snippet;
  } & (T extends string
    ? HTMLTagProps<T>
    : T extends Component<any>
      ? ComponentProps<T>
      : Record<string, any>);

  let { isActive, children, as, ...props }: Props = $props();
</script>

{#if typeof as === 'string'}
  <svelte:element
    this={as as string}
    aria-current={isActive ? 'page' : undefined}
    data-slot="pagination-link"
    data-active={isActive}
    {...props}
  >
    {@render children?.()}
  </svelte:element>
{:else}
  {@const DComponent = as as Component<any>}
  <DComponent
    aria-current={isActive ? 'page' : undefined}
    data-slot="pagination-link"
    data-active={isActive}
    {...props}
  >
    {@render children?.()}
  </DComponent>
{/if} -->
