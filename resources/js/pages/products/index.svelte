<script module lang="ts">
  import { index } from '@/routes/products';

  export const layout = {
    breadcrumbs: [
      {
        title: 'Products',
        href: index.url(),
      },
    ],
  };
</script>

<script lang="ts">
  import AppHead from '@/components/AppHead.svelte';
  import type { LaravelPaginator } from '@/types/paginate';

  interface Product {
    id: number;
    nombre: string;
    descripcion: string;
    precio: number;
    unidad_medida: string;
    stock: number;
    activo: boolean;
    // TODO: check this
    created_at: string;
    updated_at: string;
  }

  type Props = {
    products: LaravelPaginator<Product>;
  };
  let { products }: Props = $props();
</script>

<AppHead title="Productos" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
  <div
    class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default"
  >
    <table class="w-full text-sm text-left rtl:text-right text-body">
      <thead
        class="text-sm text-body bg-neutral-secondary-medium border-b border-default-medium"
      >
        <tr>
          <th scope="col" class="px-6 py-3 font-medium">Nombre</th>
          <th scope="col" class="px-6 py-3 font-medium">Descripcion</th>
          <th scope="col" class="px-6 py-3 font-medium">Precio</th>
          <th scope="col" class="px-6 py-3 font-medium">Unidad de Medida</th>
          <th scope="col" class="px-6 py-3 font-medium">Stock</th>
          <th scope="col" class="px-6 py-3 font-medium">Activo</th>
          <th scope="col" class="px-6 py-3 font-medium">Acciones</th>
        </tr>
      </thead>
      <tbody>
        {#each products.data as item (item.id)}
          <tr
            class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-900"
          >
            <th
              scope="row"
              class="px-6 py-4 font-medium text-heading whitespace-nowrap"
            >
              {item.nombre}
            </th>
            <td class="px-6 py-4">{item.descripcion}</td>
            <td class="px-6 py-4">{item.precio}</td>
            <td class="px-6 py-4">{item.unidad_medida}</td>
            <td class="px-6 py-4">{item.stock}</td>
            <td class="px-6 py-4">{item.activo}</td>
            <td class="px-6 py-4 text-right"> TODO </td>
          </tr>
        {/each}
      </tbody>
    </table>
    <nav
      class="flex flex-col items-start justify-between p-4 space-y-3 md:flex-row md:items-center md:space-y-0"
    >
      <span class="text-gray-400"
        >Mostrando <span class="font-semibold text-white"
          >{products.from}-{products.to}</span
        >
        de
        <span class="text-white">{products.total}</span></span
      >
      <ul class="inline-flex items-stretch -space-x-px">
        <li>
          <a
            href={products.prev_page_url}
            class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
          >
            <span class="sr-only">Previous</span>
            <svg
              class="w-5 h-5"
              aria-hidden="true"
              fill="currentColor"
              viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                fill-rule="evenodd"
                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                clip-rule="evenodd"
              ></path>
            </svg>
          </a>
        </li>
        {#each products.links as item (item.label)}
          {#if !item.label.includes('Previous') && !item.label.includes('Next')}
            <li>
              <a
                href={item.url}
                class={`flex items-center justify-center px-3 py-2 text-sm leading-tight border ${item.active ? 'hover:bg-primary-100 hover:text-primary-700 border-gray-700 bg-gray-700 text-white' : 'bg-gray-800 border-gray-700 text-gray-400 hover:bg-gray-700 hover:text-white'}`}
                >{item.label}</a
              >
            </li>
          {/if}
        {/each}
        <li>
          <a
            href={products.next_page_url}
            class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
          >
            <span class="sr-only">Next</span>
            <svg
              class="w-5 h-5"
              aria-hidden="true"
              fill="currentColor"
              viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"
              ></path>
            </svg>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</div>
