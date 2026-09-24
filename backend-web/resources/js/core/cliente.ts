import ClienteController from '@/actions/App/Http/Controllers/ClienteController';

const rucCache: Record<string, string> = {};

const validarRucSunat = (ruc: string) => {
  if (!/^(10|15|17|20)\d{9}$/.test(ruc)) {
    return false;
  }

  const factores = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
  let suma = 0;

  for (let i = 0; i < 10; i++) {
    suma += parseInt(ruc[i]) * factores[i];
  }

  const residuo = suma % 11;
  let digitoVerificadorEsperado = 11 - residuo;

  if (digitoVerificadorEsperado === 10) {
    digitoVerificadorEsperado = 0;
  }

  if (digitoVerificadorEsperado === 11) {
    digitoVerificadorEsperado = 1;
  }

  const digitoReal = parseInt(ruc[10]);

  return digitoVerificadorEsperado === digitoReal;
};

export const buscarRUC = async (ruc: string) => {
  ruc = ruc.trim();

  if (!ruc) {
    throw new Error('El RUC no puede estar vacio');
  }

  if (ruc.length !== 11) {
    throw new Error(
      'El RUC debe tener 11 caracteres, actualmente tiene ' + ruc.length,
    );
  }

  if (!validarRucSunat(ruc)) {
    throw new Error('El RUC no es válido');
  }

  if (ruc in rucCache) {
    const cachedValue = rucCache[ruc];
    console.log('recuperado de cache');

    // NOTE: el string puede estar vacio
    if (!cachedValue) {
      throw new Error('RUC no encontrado');
    }

    return cachedValue;
  }

  const response = await fetch(ClienteController.getByRUC.url(ruc));

  if (!response.ok) {
    rucCache[ruc] = '';

    throw new Error('RUC no encontrado');
  }

  const data = (await response.json()) as { razon_social: string };
  rucCache[ruc] = data.razon_social;

  return data.razon_social;
};
