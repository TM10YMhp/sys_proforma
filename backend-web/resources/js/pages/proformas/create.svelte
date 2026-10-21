<script module lang="ts">
  import proformasRoutes from '@/routes/proformas';

  export const layout = {
    breadcrumbs: [
      {
        title: 'Proformas',
        href: proformasRoutes.index.url(),
      },
      {
        title: 'Nuevo',
      },
    ],
  };
</script>

<script lang="ts">
  import { useForm } from '@inertiajs/svelte';
  import ProformaController from '@/actions/App/Http/Controllers/ProformaController';
  import AppHead from '@/components/AppHead.svelte';
  import type { ProductItem } from '@/types/product';
  import type { ProformaForm } from '@/types/proforma';
  import SeccionProducto from './partials/SeccionProducto.svelte';
  import SeccionProforma from './partials/SeccionProforma.svelte';
  import SeccionTabla from './partials/SeccionTabla.svelte';

  type Props = {
    codigo: string;
  };
  const { codigo }: Props = $props();

  // HACK: usar codigo en lugar de id
  // svelte-ignore state_referenced_locally
  // svelte-ignore non_reactive_update
  let form = useForm<Omit<ProformaForm, 'id'> & { codigo: string }>({
    codigo: codigo,
    fecha_emision: new Date().toISOString(),
    fecha_vencimiento: (() => {
      // TODO: esto es seguro?
      // eslint-disable-next-line svelte/prefer-svelte-reactivity
      const mañana = new Date();
      mañana.setDate(mañana.getDate() + 1);

      return mañana.toISOString();
    })(),
    subtotal: 0,
    igv_tasa: 0.18,
    productos: [],
  });

  // NOTE: $effect solo debe usarse de forma excepcional
  $effect(() => {
    form.subtotal = productos.reduce(
      (acum, producto) => acum + producto.cantidad * producto.precio,
      0,
    );
  });

  let productos = $state<ProductItem[]>([]);

  const handleSubmit = (e: SubmitEvent) => {
    e.preventDefault();

    form.productos = productos.map((p) => ({
      id: p.id,
      cantidad: p.cantidad,
    }));

    form.post(ProformaController.store.url());
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

<AppHead title="Proformas | Nuevo" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
  <form onsubmit={handleSubmit} class="space-y-2">
    <div class="flex flex-row gap-2">
      <SeccionProforma bind:form proforma={{ codigo }} />

      <SeccionProducto {form} onAddProduct={addProduct} />
    </div>

    <SeccionTabla {form} bind:productos />

    <button class="btn btn-primary" disabled={form.processing} type="submit"
      >Crear Proforma</button
    >
  </form>
</div>
