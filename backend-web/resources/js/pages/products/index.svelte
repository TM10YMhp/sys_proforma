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
  import {
    Pagination,
    PaginationContent,
    PaginationItem,
    PaginationLink,
    PaginationNext,
    PaginationPrevious,
  } from '@/components/_ui/pagination';
  import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
  } from '@/components/_ui/table';
  import AppHead from '@/components/AppHead.svelte';
  import Button from '@/components/ui/button/Button.svelte';
  import { cn } from '@/lib/utils';
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
  <!-- TODO: el cursor no se establece -->
  <Link href={ProductController.create.url()} class="w-fit">
    <Button>Nuevo Producto</Button>
  </Link>

  <Table>
    <TableHeader>
      <TableRow>
        <TableHead>#</TableHead>
        <TableHead>Codigo</TableHead>
        <TableHead>Descripcion</TableHead>
        <TableHead>Precio</TableHead>
        <TableHead>Unidad de Medida</TableHead>
        <TableHead>Stock</TableHead>
        <TableHead class="text-center">Activo</TableHead>
        <TableHead>Acciones</TableHead>
      </TableRow>
    </TableHeader>
    <TableBody>
      {#each products.data as item, idx (item.id)}
        <TableRow>
          <TableCell>{idx + Number(products.from)}</TableCell>
          <TableCell>{item.codigo}</TableCell>
          <TableCell>{item.descripcion}</TableCell>
          <TableCell>{item.precio}</TableCell>
          <TableCell>{item.unidad_medida}</TableCell>
          <TableCell>{item.stock}</TableCell>
          <TableCell class="text-center">
            {#if item.activo}
              <span class="inline-block bg-green-500 size-3 rounded-full"
              ></span>
            {:else}
              <span class="inline-block bg-red-500 size-3 rounded-full"></span>
            {/if}
          </TableCell>
          <TableCell>
            <Link href={ProductController.edit(item.id)}>
              <Button class="bg-slate-500 hover:bg-slate-700">Editar</Button>
            </Link>
            <Button
              disabled={form.processing}
              class="bg-red-500 hover:bg-red-700"
              onclick={() => handleDelete(item)}
            >
              Borrar
            </Button>
          </TableCell>
        </TableRow>
      {/each}
    </TableBody>
  </Table>

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
