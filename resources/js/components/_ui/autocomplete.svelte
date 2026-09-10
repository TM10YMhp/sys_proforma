<script lang="ts">
  import type { HTMLInputAttributes } from 'svelte/elements';
  import ProductController from '@/actions/App/Http/Controllers/ProductController';
  import { cn } from '@/lib/utils';

  type Props = Omit<HTMLInputAttributes, 'type' | 'autocomplete'> & {
    delay?: number;
    limit?: number;
    value: string;
  };

  type TestProduct = {
    nombre: string;
    precio: number;
  };

  const uid = $props.id();
  let {
    class: className,
    delay = 300,
    limit = 1,
    value: busqueda = $bindable(),
    ...props
  }: Props = $props();

  let resultados = $state<TestProduct[]>([]);
  let buscando = $state(false);
  let timeoutId: number;

  function buscarProductos() {
    clearTimeout(timeoutId);

    if (busqueda.trim().length < limit) {
      resultados = [];

      return;
    }

    timeoutId = setTimeout(async () => {
      buscando = true;

      try {
        // const response = await fetch(
        //   `products/search?q=${encodeURIComponent(busqueda)}`,
        // );
        // https://github.com/laravel/wayfinder#query-parameters
        const response = await fetch(
          ProductController.search.url({
            query: {
              q: busqueda,
            },
          }),
        );
        resultados = await response.json();
      } catch (error) {
        console.error('Error buscando productos:', error);
      } finally {
        buscando = false;
      }
    }, delay);
  }

  function seleccionar(producto: TestProduct) {
    busqueda = producto.nombre;
    resultados = [];
  }

  const highlightResult = (text: string) => {
    if (!busqueda.trim()) {
      return text;
    }

    const searchLower = busqueda.toLowerCase();
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

<!-- TODO: separar por bloques -->
<div class="relative max-w-80" data-slot="autocomplete-container">
  <div class="relative flex items-center">
    <input
      id={uid}
      bind:value={busqueda}
      oninput={buscarProductos}
      class={cn('w-full border rounded bg-background', className)}
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
      class="absolute bg-background inset-x-0 rounded border border-t-0 border-stone-500 overflow-y-auto max-h-50"
    >
      {#each resultados as producto, idx (idx)}
        <li>
          <button
            onclick={() => seleccionar(producto)}
            class="w-full flex justify-between cursor-pointer hover:bg-stone-800"
          >
            <!-- eslint-disable-next-line svelte/no-at-html-tags -->
            <span>{@html highlightResult(producto.nombre)}</span>
            <span class="text-[#10b981] font-bold">${producto.precio}</span>
          </button>
        </li>
      {/each}
    </ul>
  {/if}
</div>
