<script lang="ts">
  import type { HTMLInputAttributes } from 'svelte/elements';
  import { cn } from '@/lib/utils';

  type Props = Omit<
    HTMLInputAttributes,
    'type' | 'autocomplete' | 'onclick'
  > & {
    delay?: number;
    limit?: number;
    urlTemplate: string;
    value: string;
    onAccept?: (e: string) => void;
  };

  let {
    class: className,
    delay = 300,
    limit = 1,
    value = $bindable(),
    urlTemplate,
    onAccept,
    ...props
  }: Props = $props();

  let resultados = $state<string[]>([]);
  let buscando = $state(false);
  let timeoutId: number;

  // TODO: incluir cache de ruc
  function buscarProductos() {
    clearTimeout(timeoutId);

    if (value.trim().length < limit) {
      resultados = [];

      return;
    }

    timeoutId = setTimeout(async () => {
      buscando = true;

      try {
        // https://github.com/laravel/wayfinder#query-parameters
        // const response = await fetch(
        //   ProductController.search.url({ query: { q: value } }),
        // );
        const response = await fetch(urlTemplate.replace('%s', value));
        resultados = await response.json();
      } catch (error) {
        console.error('Error al buscar:', error);
      } finally {
        buscando = false;
      }
    }, delay);
  }

  function handleClick(item: string) {
    value = item;
    resultados = [];
  }

  const highlightResult = (text: string) => {
    if (!value.trim()) {
      return text;
    }

    const searchLower = value.toLowerCase();
    let searchIndex = 0;

    return text
      .split('')
      .map((char) => {
        const charLower = char.toLowerCase();

        if (
          searchIndex < searchLower.length &&
          charLower === searchLower[searchIndex]
        ) {
          searchIndex++;

          return `<span style="color: #10b981">${char}</span>`;
        }

        return char;
      })
      .join('');
  };
</script>

<div class="relative max-w-80" data-slot="autocomplete-container">
  <div class="relative flex items-center">
    <input
      bind:value
      oninput={buscarProductos}
      class={cn('input', className)}
      {...props}
      autocomplete="off"
      type="text"
    />
    {#if buscando}
      <span class="absolute right-2.5">⏳</span>
    {/if}
  </div>

  {#if resultados.length > 0}
    <ul
      class="absolute inset-x-0 overflow-y-auto max-h-32 z-10 list bg-base-100 rounded-box"
    >
      {#each resultados as item, idx (idx)}
        <li class="list-row p-0">
          <button
            onclick={() => {
              handleClick(item);
              onAccept?.(item);
            }}
            class="list-col-grow btn btn-ghost px-2 flex justify-between"
          >
            <!-- eslint-disable-next-line svelte/no-at-html-tags -->
            <span>{@html highlightResult(item)}</span>
            <!-- <span class="text-[#10b981] font-bold">${item.precio}</span> -->
          </button>
        </li>
      {/each}
    </ul>
  {/if}
</div>
