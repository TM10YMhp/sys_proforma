import Excel from '@excel.js/exceljs';

const workbook = new Excel.Workbook();
// workbook.views = [
//   {
//     x: 0,
//     y: 0,
//     width: 10000,
//     height: 20000,
//     firstSheet: 0,
//     activeTab: 1,
//     visibility: 'visible',
//   },
// ];

const ws = workbook.addWorksheet('Proforma', {
  pageSetup: {
    margins: {
      top: 1.1,
      bottom: 0.9,
      left: 0.3,
      right: 0.3,
      header: 0.8,
      footer: 0.6,
    },
    paperSize: 9,
    scale: 85,
  },
});

const defaultStyle: Partial<Excel.Style> = {
  font: {
    size: 10,
  },
  alignment: {
    wrapText: true,
    horizontal: 'center',
    vertical: 'middle',
  },
};

// Apply styles to worksheet columns
// TODO: los estilos por columna no funcionan como se espera
ws.columns = [
  { header: 'Nº', key: 'number', width: 3, style: defaultStyle },
  {
    header: 'CODIGO PRODUCTO',
    key: 'codigo_producto',
    width: 9,
    style: defaultStyle,
  },
  {
    header: 'DESCRIPCIÓN',
    key: 'descripcion',
    width: 62,
    style: defaultStyle,
  },
  { header: 'CANTIDAD', key: 'cantidad', width: 7, style: defaultStyle },
  { header: 'U. MEDIDA', key: 'medida', width: 10, style: defaultStyle },
  {
    header: 'PRECIO UNITARIO',
    key: 'precio_unitario',
    width: 9,
    style: defaultStyle,
  },
  { header: 'TOTAL', key: 'total', width: 10, style: defaultStyle },
];

ws.getRow(1).font = {
  size: 10,
  bold: true,
};

const data = {
  products: [
    {
      descripcion: 'Cartel vinil en base celtex 3 mm de 30 cm x 22 cm',
      cantidad: 27,
      medida: 'UN',
      precio_unitario: 10,
      total: 270,
    },
    {
      descripcion: 'Cartel vinil en base celtex 3 mm de 60 cm x 30 cm',
      cantidad: 20,
      medida: 'UN',
      precio_unitario: 24,
      total: 480,
    },
    {
      descripcion: 'Cartel vinil en base celtex 3 mm de 30 cm x 30 cm',
      cantidad: 8,
      medida: 'UN',
      precio_unitario: 12.5,
      total: 100,
    },
    {
      descripcion: 'Cartel vinil en base celtex 3 mm de 30 cm x 25 cm',
      cantidad: 2,
      medida: 'UN',
      precio_unitario: 10,
      total: 20,
    },
    {
      descripcion: 'Cartel vinil en base celtex 3 mm de 100 cm x 60 cm',
      cantidad: 6,
      medida: 'UN',
      precio_unitario: 65,
      total: 390,
    },
    {
      descripcion: 'Cartel vinil en base celtex 3 mm de 40 cm x 30 cm',
      cantidad: 8,
      medida: 'UN',
      precio_unitario: 15,
      total: 120,
    },
    {
      descripcion: 'Cartel vinil adhesivo 15 cm x 21 cm',
      cantidad: 20,
      medida: 'UN',
      precio_unitario: 2.5,
      total: 50,
    },
    {
      descripcion: 'Cartel vinil adhesivo 25 cm x 10 cm',
      cantidad: 22,
      medida: 'UN',
      precio_unitario: 3.5,
      total: 77,
    },
    {
      descripcion: 'Cartel vinil adhesivo 20 cm x 20 cm',
      cantidad: 20,
      medida: 'UN',
      precio_unitario: 4,
      total: 180,
    },
    {
      descripcion: 'Cartel vinil adhesivo A4',
      cantidad: 12,
      medida: 'UN',
      precio_unitario: 5,
      total: 60,
    },
    {
      descripcion: 'Cartel vinil en base celtex 3 mm de 30 cm x 20 cm',
      cantidad: 20,
      medida: 'UN',
      precio_unitario: 9,
      total: 180,
    },
  ],
};

// ws.getRow(2).values = [
//   1,
//   0,
//   'Cartel vinil en base celtex 3 mm de 30 cm x 22 cm',
//   27,
//   'UN',
//   10,
//   270,
// ];

const products = data.products.map((x) => [
  x.descripcion,
  x.cantidad,
  x.medida,
  x.precio_unitario,
  x.total,
]);
// ws.getRow(2).values = [1, '', ...products[0]];

for (let i = 0; i < 30; i++) {
  const product = products.at(i);

  if (!product) {
    ws.getRow(i + 2).values = [i + 1, '', '', '', '', '', ''];
  } else {
    const row = ws.getRow(i + 2);
    row.values = [i + 1, '', ...product];
    row.getCell(3).alignment = {
      horizontal: 'left',
    };
    row.getCell(6).numFmt = '#.00';
    row.getCell(7).numFmt = '#.00';
  }
}

// Set Column 3 to Currency Format
// ws.getColumn(3).numFmt = '"£"#,##0.00;[Red]-"£"#,##0.00';

// Set Row 2 to Comic Sans.
// ws.getRow(2).font = {
//   name: 'Comic Sans MS',
//   family: 4,
//   size: 16,
//   underline: 'double',
//   bold: true,
// };

ws.getRow(1).eachCell((cell) => {
  cell.border = {
    top: { style: 'thin' },
    left: { style: 'thin' },
    bottom: { style: 'thin' },
    right: { style: 'thin' },
  };
  cell.fill = {
    type: 'pattern',
    pattern: 'solid',
    fgColor: { argb: 'C0C0C0' },
  };
});

const rows = ws.getRows(2, 30);
rows?.forEach((row) => {
  row.eachCell((cell) => {
    cell.border = {
      top: { style: 'thin' },
      left: { style: 'thin' },
      bottom: { style: 'thin' },
      right: { style: 'thin' },
    };
  });
});

await workbook.xlsx.writeFile('test.xlsx');

// const workbook = new Excel.Workbook();
// await workbook.xlsx.readFile("proforma.xlsx");

// console.log(workbook.worksheets.at(0)?.properties)
