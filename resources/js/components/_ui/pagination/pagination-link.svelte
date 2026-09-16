<script lang="ts">
  import type { HTMLAnchorAttributes } from 'svelte/elements';
  let {
    isActive,
    children,
    ...props
  }: HTMLAnchorAttributes & {
    // TODO: check this
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


<!-- TODO:
- funciona pero afecta a otros componentes
- los tipos no se infieren correctamente desde afuera -->
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
