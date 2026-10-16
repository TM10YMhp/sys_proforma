<script module lang="ts">
  import proformasRoutes from '@/routes/proformas';

  export const layout = {
    breadcrumbs: [
      {
        title: 'Proformas',
        href: proformasRoutes.index.url(),
      },
      {
        title: 'Editar',
      },
    ],
  };
</script>

<script lang="ts">
  import { useForm } from '@inertiajs/svelte';
  import AppHead from '@/components/AppHead.svelte';
  import type { ProductItem } from '@/types/product';
  import type { Proforma, ProformaForm } from '@/types/proforma';
  import SeccionProducto from './partials/SeccionProducto.svelte';
  import SeccionProforma from './partials/SeccionProforma.svelte';
  import SeccionTabla from './partials/SeccionTabla.svelte';

  type Props = {
    proforma: Proforma;
  };
  let { proforma }: Props = $props();

  // svelte-ignore state_referenced_locally
  let productos = $state<ProductItem[]>(
    proforma.products.map((x) => ({
      id: x.id,
      codigo: x.codigo,
      descripcion: x.descripcion,
      precio: x.pivot.precio_unitario,
      unidad_medida: x.unidad_medida,
      cantidad: x.pivot.cantidad,
      activo: x.activo,
    })),
  );

  // svelte-ignore state_referenced_locally
  // svelte-ignore non_reactive_update
  let form = useForm<ProformaForm>({
    id: proforma.id,
    fecha_emision: proforma.fecha_emision,
    fecha_vencimiento: proforma.fecha_vencimiento,
    subtotal: proforma.subtotal,
    igv_tasa: proforma.igv_tasa,
    productos: proforma.products.map((x) => ({
      id: x.pivot.product_id,
      cantidad: x.pivot.cantidad,
    })),
  });

  const handleSubmit = (e: SubmitEvent) => {
    e.preventDefault();

    // form.subtotal = Number(obtenerSubtotal().toFixed(2));
    form.productos = productos.map((p) => ({
      id: p.id,
      cantidad: p.cantidad,
    }));

    console.log('hola', form.data());

    return;

    // form.put(ProformaController.update.url(proforma.id));
  };

  const addProduct = (productItem: ProductItem) => {
    const producto = productos.find((p) => p.id === productItem.id);

    if (producto) {
      producto.cantidad += productItem.cantidad;
    } else {
      productos.unshift({ ...productItem });
    }
  };
</script>

<AppHead title="Proformas | Editar" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
  <form onsubmit={handleSubmit} class="space-y-2">
    <div class="flex flex-row gap-2">
      <SeccionProforma bind:form {productos} {proforma} />

      <SeccionProducto {form} onAddProduct={addProduct} />

      <fieldset class="fieldset rounded-box border border-base-100 px-2 w-xs">
        <legend class="fieldset-legend px-2 leading-none">Cliente</legend>

        <label>
          <span class="label">Nombres</span>
          <input class="input" />
        </label>
        <div class="flex flex-row gap-4">
          <label>
            <span class="label">Primer Apellido</span>
            <input class="input" />
          </label>
          <label>
            <span class="label">Segundo Apellido</span>
            <input class="input" />
          </label>
        </div>
        <div class="flex flex-row gap-4">
          <label>
            <span class="label">RUC</span>
            <input type="number" class="input" />
          </label>
          <label>
            <span class="label">DNI</span>
            <input type="number" class="input" />
          </label>
          <label>
            <span class="label">Telefono</span>
            <input class="input" />
          </label>
        </div>

        <button
          class="btn btn-secondary mt-2"
          disabled={form.processing}
          type="button"
          onclick={(_) => {}}>Establecer Cliente</button
        >
      </fieldset>
    </div>

    <SeccionTabla {form} bind:productos />

    <button class="btn btn-primary" disabled={form.processing} type="submit"
      >Actualizar Proforma</button
    >
  </form>
</div>
