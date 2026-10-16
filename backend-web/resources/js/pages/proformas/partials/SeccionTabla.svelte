<script lang="ts">
  import type { InertiaForm } from '@inertiajs/svelte';
  import { MinusIcon, PlusIcon, XIcon } from 'lucide-svelte';
  import { table } from '@/lib/variants';
  import type { ProductItem } from '@/types/product';
  import type { ProformaForm } from '@/types/proforma';

  type Props = {
    form: InertiaForm<ProformaForm>;
    productos: ProductItem[];
  };
  let { form, productos = $bindable() }: Props = $props();

  const incrementarCantidad = (id: number) => {
    const producto = productos.find((p) => p.id === id);

    if (producto) {
      producto.cantidad++;
    }
  };

  const decrementarCantidad = (id: number) => {
    const producto = productos.find((p) => p.id === id);

    if (!producto) {
      return;
    }

    if (producto.cantidad > 1) {
      producto.cantidad--;
    } else {
      productos = productos.filter((p) => p.id !== id);
    }
  };

  const eliminarProducto = (id: number) => {
    productos = productos.filter((p) => p.id !== id);
  };
</script>

<div class="overflow-x-auto">
  <table class={table()}>
    <thead>
      <tr>
        <th>#</th>
        <th>Codigo</th>
        <th>Descripcion</th>
        <th class="text-right">Precio</th>
        <th class="text-center">U. Medida</th>
        <th class="text-center">Cantidad</th>
        <th class="text-center">Total</th>
        <th class="text-center">Activo</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      {#each productos as item, idx (idx)}
        <tr>
          <th>{idx + 1}</th>
          <td class="font-mono">{item.codigo}</td>
          <td class="truncate max-w-50">{item.descripcion}</td>
          <td class="font-mono text-right">{item.precio}</td>
          <td class="text-center">{item.unidad_medida}</td>
          <td class="text-center">{item.cantidad}</td>
          <td class="font-mono text-right"
            >{(item.precio * item.cantidad).toFixed(2)}</td
          >
          <td class="text-center">
            {#if item.activo}
              <span class="inline-block bg-green-500 size-3 rounded-full"
              ></span>
            {:else}
              <span class="inline-block bg-red-500 size-3 rounded-full"></span>
            {/if}
          </td>
          <td class="flex flex-row gap-1">
            <button
              disabled={form.processing}
              class="btn btn-square btn-info"
              onclick={() => incrementarCantidad(item.id)}
            >
              <PlusIcon />
            </button>
            <button
              disabled={form.processing}
              class="btn btn-square btn-error"
              onclick={() => decrementarCantidad(item.id)}
            >
              <MinusIcon />
            </button>
            <div class="divider divider-horizontal mx-1"></div>
            <button
              disabled={form.processing}
              class="btn btn-square btn-error btn-soft"
              onclick={() => eliminarProducto(item.id)}
            >
              <XIcon />
            </button>
          </td>
        </tr>
      {/each}
    </tbody>
  </table>
</div>
