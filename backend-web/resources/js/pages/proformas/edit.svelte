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
  import InputError from '@/components/InputError.svelte';
  import type { ProductItem } from '@/types/product';
  import type { Proforma, ProformaForm } from '@/types/proforma';
  import SeccionProducto from './partials/SeccionProducto.svelte';
  import SeccionTabla from './partials/SeccionTabla.svelte';

  type Props = {
    proforma: Proforma;
  };
  let { proforma }: Props = $props();

  // svelte-ignore state_referenced_locally
  let info = $state(proforma);

  // svelte-ignore state_referenced_locally
  let porcentaje = $state(proforma.igv_tasa * 100);
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
  const form = useForm<ProformaForm>({
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

  const stringToDatetime = (fecha_utc: string) => {
    const dateLocal = new Date(fecha_utc);
    const offset = dateLocal.getTimezoneOffset() * 60000;
    const localISODate = new Date(dateLocal.getTime() - offset).toISOString();

    return localISODate.slice(0, 16);
  };

  // TODO: solo es necesario como valor inicial
  let fecha_emision = $state(stringToDatetime(form.fecha_emision));
  let fecha_vencimiento = $state(stringToDatetime(form.fecha_vencimiento));

  const datetimeToUTC = (datetime: string) => {
    return new Date(datetime).toISOString();
  };

  const handleSubmit = (e: SubmitEvent) => {
    e.preventDefault();

    form.subtotal = Number(obtenerSubtotal().toFixed(2));
    form.productos = productos.map((p) => ({
      id: p.id,
      cantidad: p.cantidad,
    }));

    console.log(form.data());

    return;

    // eslint-disable-next-line no-unreachable
    form.igv_tasa = porcentaje / 100;
    form.fecha_emision = datetimeToUTC(fecha_emision);
    form.fecha_vencimiento = datetimeToUTC(fecha_vencimiento);
    form.put(ProformaController.update.url(proforma.id));
  };

  const obtenerSubtotal = () => {
    return productos.reduce(
      (acum, producto) => acum + producto.cantidad * producto.precio,
      0,
    );
  };

  const obtenerMontoIgv = () => {
    return obtenerSubtotal() * info.igv_tasa;
  };

  const obtenerTotal = () => {
    return obtenerMontoIgv() + obtenerSubtotal();
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
      <fieldset class="fieldset rounded-box border border-base-100 px-2">
        <legend class="fieldset-legend px-2 leading-none mb-0">Proforma</legend>

        <div
          class="grid grid-cols-[auto_1fr] gap-x-2 [&>*:nth-child(odd)]:font-bold [&>*:nth-child(odd)]:text-right"
        >
          <span>Codigo:</span>
          <span>{info.codigo}</span>
          <span>Subtotal:</span>
          <span>{obtenerSubtotal().toFixed(2)}</span>
          <span>IGV Monto:</span>
          <span>{obtenerMontoIgv().toFixed(2)}</span>
          <span>Total:</span>
          <span>{obtenerTotal().toFixed(2)}</span>
        </div>

        <label for="emision" class="label">Fecha de Emision</label>
        <input
          id="emision"
          class="input font-mono"
          type="datetime-local"
          bind:value={fecha_emision}
        />
        <InputError message={form.errors.fecha_emision} />

        <label for="vencimiento" class="label">Fecha de Vencimiento</label>
        <input
          id="vencimiento"
          class="input font-mono"
          type="datetime-local"
          bind:value={fecha_vencimiento}
        />
        <InputError message={form.errors.fecha_vencimiento} />

        <label for="igv_tasa" class="label">IGV Tasa (%)</label>
        <input
          class="input w-fit"
          id="igv_tasa"
          bind:value={porcentaje}
          type="number"
          min="0"
          max="100"
          step="0.1"
        />
        <InputError message={form.errors.igv_tasa} />
      </fieldset>

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
