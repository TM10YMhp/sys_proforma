<script module lang="ts">
  import { index } from '@/routes/products';

  export const layout = {
    breadcrumbs: [
      {
        title: 'Productos',
        href: index.url(),
      },
    ],
  };
</script>

<script lang="ts">
  import { Link, useForm } from '@inertiajs/svelte';
  import ProductController from '@/actions/App/Http/Controllers/ProductController';
  import AppHead from '@/components/AppHead.svelte';
  import { cn } from '@/lib/utils';
  import { table } from '@/lib/variants';
  import type { LaravelPaginator } from '@/types/paginate';
  import type { Product } from '@/types/product';

  type Props = {
    products: LaravelPaginator<Product>;
  };
  let { products }: Props = $props();

  const form = useForm();

  const handleDelete = (product: Product) => {
    if (
      confirm(`Estas seguro que desea eliminar el producto: ${product.codigo}?`)
    ) {
      form.delete(ProductController.destroy.url(product.id));
    }
  };
</script>

<AppHead title="Productos" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
  <Link href={ProductController.create.url()} class="btn btn-primary w-fit">
    Nuevo Producto
  </Link>

  <div class="overflow-x-auto">
    <table class={table()}>
      <thead>
        <tr>
          <th>#</th>
          <th>Codigo</th>
          <th>Descripcion</th>
          <th class="text-right">Precio</th>
          <th class="text-center">U. Medida</th>
          <th class="text-center">Stock</th>
          <th class="text-center">Activo</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        {#each products.data as item, idx (item.id)}
          <tr>
            <th>{idx + Number(products.from)}</th>
            <td class="font-mono">{item.codigo}</td>
            <td class="truncate max-w-50">{item.descripcion}</td>
            <td class="font-mono text-right">{item.precio}</td>
            <td class="text-center">{item.unidad_medida}</td>
            <td class="text-center">{item.stock}</td>
            <td class="text-center">
              {#if item.activo}
                <span class="inline-block bg-green-500 size-3 rounded-full"
                ></span>
              {:else}
                <span class="inline-block bg-red-500 size-3 rounded-full"
                ></span>
              {/if}
            </td>
            <td class="flex flex-row gap-1">
              <Link href={ProductController.edit(item.id)} class="btn btn-info">
                Editar
              </Link>
              <button
                disabled={form.processing}
                class="btn btn-error"
                onclick={() => handleDelete(item)}
              >
                Borrar
              </button>
            </td>
          </tr>
        {/each}
      </tbody>
    </table>
  </div>

  <div class="flex flex-row justify-between p-4">
    <span class="text-gray-400"
      >Mostrando <span class="font-semibold text-white"
        >{products.from}-{products.to}</span
      >
      de
      <span class="text-white">{products.total}</span></span
    >

    <!-- NOTE: prefetch click necesita el cacheFor sino no funciona -->
    <div class="join">
      <Link
        prefetch="click"
        cacheFor="3m"
        href={products.prev_page_url!}
        class="join-item btn btn-soft">«</Link
      >
      {#each products.links as item (item.label)}
        {#if !item.label.includes('Previous') && !item.label.includes('Next')}
          <Link
            prefetch="click"
            cacheFor="3m"
            href={item.url!}
            class={cn('join-item btn btn-soft', item.active && 'btn-active')}
            >{item.label}</Link
          >
        {/if}
      {/each}
      <Link
        prefetch="click"
        cacheFor="3m"
        href={products.next_page_url!}
        class="join-item btn btn-soft">»</Link
      >
    </div>
  </div>
</div>
