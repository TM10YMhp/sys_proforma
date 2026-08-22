<script module lang="ts">
  import productRoutes from '@/routes/products';

  export const layout = {
    breadcrumbs: [
      {
        title: 'Productos',
        href: productRoutes.index.url(),
      },
      {
        title: 'Nuevo',
        href: productRoutes.create.url(),
      },
    ],
  };
</script>

<script lang="ts">
  import AppHead from '@/components/AppHead.svelte';
  import Button from '@/components/ui/button/Button.svelte';
  import ProductController from '@/actions/App/Http/Controllers/ProductController';
  import Input from '@/components/ui/input/Input.svelte';
  import { useForm } from '@inertiajs/svelte';
  import { Label } from '@/components/ui/label';
  import InputError from '@/components/InputError.svelte';
  import type { Product } from '@/types/product';

  const form = useForm<Omit<Product, 'id' | 'created_at' | 'updated_at'>>({
    nombre: '',
    descripcion: '',
    stock: 0,
    precio: 0,
    unidad_medida: '',
    activo: true,
  });

  const handleSubmit = (e: SubmitEvent) => {
    e.preventDefault();
    form.post(ProductController.store.url());
  };
</script>

<AppHead title="Productos | Nuevo" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
  <form onsubmit={handleSubmit} class="w-8/12 space-y-4">
    <div class="grid gap-2">
      <Label for="nombre">Nombre</Label>
      <Input id="nombre" bind:value={form.nombre} />
      <InputError message={form.errors.nombre} />
    </div>
    <div class="grid gap-2">
      <Label for="descripcion">Descripcion</Label>
      <textarea id="descripcion" bind:value={form.descripcion}></textarea>
      <InputError message={form.errors.descripcion} />
    </div>
    <div class="grid gap-2">
      <Label for="stock">Stock</Label>
      <Input id="stock" type="number" bind:value={form.stock} />
      <InputError message={form.errors.stock} />
    </div>
    <div class="grid gap-2">
      <Label for="precio">Precio</Label>
      <Input id="precio" type="number" step="0.01" bind:value={form.precio} />
      <InputError message={form.errors.precio} />
    </div>
    <div class="grid gap-2">
      <Label for="unidad_medida">Unidad de Medida</Label>
      <Input id="unidad_medida" bind:value={form.unidad_medida} />
      <InputError message={form.errors.unidad_medida} />
    </div>
    <Button disabled={form.processing} type="submit">Crear Producto</Button>
  </form>
</div>
