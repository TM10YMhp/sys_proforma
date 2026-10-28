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
  import ProformaController from '@/actions/App/Http/Controllers/ProformaController';
  import AppHead from '@/components/AppHead.svelte';
  import type { ProductItem } from '@/types/product';
  import type { Proforma, ProformaForm } from '@/types/proforma';
  import SeccionCliente from './partials/SeccionCliente.svelte';
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
    cliente_id: proforma.cliente_id,
  });

  // NOTE: $effect solo debe usarse de forma excepcional
  $effect(() => {
    form.subtotal = productos.reduce(
      (acum, producto) => acum + producto.cantidad * producto.precio,
      0,
    );
  });

  const handleSubmit = (e: SubmitEvent) => {
    e.preventDefault();

    form.productos = productos.map((p) => ({
      id: p.id,
      cantidad: p.cantidad,
    }));

    form.put(ProformaController.update.url(proforma.id));
  };

  const addProduct = (productItem: ProductItem) => {
    const producto = productos.find((p) => p.id === productItem.id);

    if (producto) {
      producto.cantidad += productItem.cantidad;
    } else {
      productos.unshift({ ...productItem });
    }
  };

  const handleAcceptCliente = (cliente_id: number) => {
    form.cliente_id = cliente_id;
  };
</script>

<AppHead title="Proformas | Editar" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
  <form onsubmit={handleSubmit} class="space-y-2">
    <div class="flex flex-row gap-2">
      <SeccionProforma bind:form {proforma} />

      <SeccionProducto {form} onAddProduct={addProduct} />

      <SeccionCliente {proforma} onAcceptCliente={handleAcceptCliente} />
    </div>

    <SeccionTabla {form} bind:productos />

    <button class="btn btn-primary" disabled={form.processing} type="submit"
      >Actualizar Proforma</button
    >
  </form>
</div>
