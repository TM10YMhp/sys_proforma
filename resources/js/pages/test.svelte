<script lang="ts">
  import Autocomplete from '@/components/_ui/autocomplete.svelte';

  let busqueda = $state('');
</script>

<label for="prueba">Agregar Producto:</label>
<Autocomplete
  id="prueba"
  bind:value={busqueda}
  placeholder="Escribe el nombre del producto..."
/>
