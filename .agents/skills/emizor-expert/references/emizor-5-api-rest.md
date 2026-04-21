# Documentación Técnica – API REST Emizor 5

**INFORMACIÓN**

| NOMBRE          | CARGO                  | Versión | FECHA      |
|-----------------|------------------------|---------|------------|
| ELABORADO POR: | Denis Silisqui A.     | 1.2     | 24/02/2025 |

**Responsable de Desarrollo**

---

## Contenido

- [Introducción](#introducción) ................................................ 3
- [Autenticación](#autenticación) ............................................. 3
  - [Obtención del token](#obtención-del-token) ....................... 3
  - [Headers requeridos](#headers-requeridos) ........................... 3
- [Base URL (desarrollo)](#base-url-desarrollo) ....................... 3
- [Endpoints](#endpoints) ...................................................... 4
  1. [Verificar NIT](#1-verificar-nit) ..................................... 4
  2. [Crear Cliente](#2-crear-cliente) ..................................... 4
  3. [Emitir Factura o Prefactura](#3-emitir-factura-o-prefactura) ..... 6
  4. [Generar QR para Prefactura](#4-generar-qr-para-prefactura) ...... 12
  5. [Emitir Prefactura](#5-emitir-prefactura) .......................... 13
  6. [Consultar Estado de Factura](#6-consultar-estado-de-factura) .... 14
  7. [Listar Productos](#7-listar-productos) ............................ 14
- [Flujo de Integración](#flujo-de-integración) .......................... 15
  - [Caso 1: Emisión directa de factura](#caso-1-emisión-directa-de-factura) ... 15
  - [Caso 2: Factura con QR](#caso-2-factura-con-qr) ................... 15
- [Códigos de Estado Comunes](#códigos-de-estado-comunes) ............. 16
- [Notas Adicionales](#notas-adicionales) .................................. 16
- [8. Crear Productos](#8-crear-productos) ............................... 17
- [9. Actualizar Productos](#9-actualizar-productos) .................... 18
- [10. Eliminar Productos](#10-eliminar-productos) ...................... 19
- [11. Listar Paramétricas](#11-listar-paramétricas) .................... 20

---

## Introducción

Esta documentación describe los servicios disponibles en la **API REST de Emizor 5**, plataforma de facturación electrónica para Bolivia.

La API permite la creación de clientes, emisión de facturas, generación de códigos QR y consulta de estados ante el SIN (Servicio de Impuestos Nacionales).

## Autenticación

Para consumir los servicios, es necesario contar con un `X-Api-Token`.

### Obtención del token

1. Ingresa a la plataforma Emizor con tu usuario y contraseña.
2. Dirígete a: **Configuración → Gestión de cuentas → Integraciones → API Tokens**.
3. Crea un nuevo token asignándole un nombre.
4. Al guardar, se te solicitará la contraseña de tu usuario.
5. Una vez registrado, copia el token generado.

### Headers requeridos

| Header              | Descripción                          |
|---------------------|--------------------------------------|
| `X-Api-Token`       | Token generado en la plataforma      |
| `X-Api-Secret`      | `password`                           |
| `X-Requested-With`  | `XMLHttpRequest`                     |
| `Accept`            | `application/json`                   |
| `Content-Type`      | `application/json`                   |

### Base URL (desarrollo)

```
https://{env}.emizor.com
```

---

## Endpoints

### 1. Verificar NIT

**GET** `/api/v1/clientfel/verify_nit/{nit}`

Valida si un NIT existe ante el SIN. Se usa para determinar el valor del campo `codigoExcepcion` en la factura.

**Parámetro**

| Parámetro | Tipo   | Descripción                  |
|-----------|--------|------------------------------|
| `nit`     | string | NIT del cliente a verificar |

**Respuesta exitosa (NIT activo)**

```json
{
  "success": true,
  "message": "NIT ACTIVO",
  "codigo": 986
}
```

**Respuesta (NIT inexistente)**

```json
{
  "success": false,
  "message": "NIT INEXISTENTE",
  "codigo": 994
}
```

**Nota:** Si `success` es `true`, el `codigoExcepcion` debe ser `0`; en caso contrario, debe ser `1`.

---

### 2. Crear Cliente

**POST** `/api/v1/clients`

Registra un cliente en la plataforma. Es necesario para asociar facturas.

**Campos**

| Campo                          | Tipo     | Descripción                              |
|--------------------------------|----------|------------------------------------------|
| `name`                         | string   | Nombre del cliente o empresa             |
| `phone`                        | string   | Teléfono                                 |
| `number`                       | string   | Código único del cliente (opcional)      |
| `felData.type_document_id`     | string   | Tipo de documento: `5` = NIT, `1` = CI   |
| `felData.business_name`        | string   | Razón social                             |
| `felData.document_number`      | string   | Número de documento                      |
| `contacts[].first_name`        | string   | Nombre del contacto                      |
| `contacts[].last_name`         | string   | Apellido                                 |
| `contacts[].email`             | string   | Correo de envío de facturas              |
| `contacts[].send_email`        | boolean  | Enviar factura por correo                |

**Ejemplo de envío**

```json
{
  "name": "Denis",
  "private_notes": "",
  "public_notes": "",
  "address1": "",
  "phone": "71558926",
  "number": "6638075",
  "felData": {
    "type_document_id": "1",
    "business_name": "Silisqui Denis",
    "document_number": "6638075",
    "complement": null
  },
  "contacts": [
    {
      "first_name": "denis",
      "last_name": "silisqui aramayo",
      "email": "denis@vendisqr.com",
      "phone": "6638075",
      "send_email": true
    }
  ]
}
```

**Ejemplo de respuesta**

```json
{
  "data": {
    "id": "9wdLgGRDbj",
    "user_id": "WjnegPGawZ",
    "name": "Denis",
    "private_notes": "",
    "public_notes": "",
    "client_hash": "EFkkOK5m8NKllJQaQeHbvSO277Bq8wRc044HFQaf",
    "address1": "",
    "phone": "71558926",
    "is_deleted": false,
    "updated_at": 1774452419,
    "archived_at": 0,
    "created_at": 1774452419,
    "display_name": "Denis",
    "number": "6638075",
    "felData": {
      "company_id": "l4zbqLyapr",
      "type_document_id": "1",
      "document_number": "6638075",
      "complement": null,
      "business_name": "Silisqui"
    },
    "contacts": [
      {
        "id": "w9aAnKlpbv",
        "first_name": "denis",
        "last_name": "silisqui aramayo",
        "email": "denis@vendisqr.com",
        "created_at": 1774452419,
        "updated_at": 1774452419,
        "archived_at": 0,
        "phone": "6638075",
        "contact_key": "iz6CZzWIUW5sEYmoQmw5Myy9LAefOA1ewGM6XcJB",
        "send_email": true,
        "link": "https://felapp.emizor.com/client/key_login/iz6CZzWIUW5sEYmoQmw5Myy9LAefOA1ewGM6XcJB"
      }
    ]
  }
}
```

---

### 3. Emitir Factura o Prefactura

**POST** `/api/v1/invoices?should_emit=true&paid=false&send_mail=true`

**Parámetros de query**

| Parámetro     | Descripción                              |
|---------------|------------------------------------------|
| `should_emit` | `true` = emisión directa, `false` = prefactura |
| `paid`        | Indica si está pagada                    |
| `send_mail`   | Enviar factura por correo                |

**Campos clave en el body**

| Campo                        | Descripción                                      |
|------------------------------|--------------------------------------------------|
| `client_id`                  | ID del cliente obtenido en la creación           |
| `line_items[].product_id`    | ID del producto (obtenido vía API de productos)  |
| `felData.codigoMetodoPago`   | `5` = Efectivo, `86` = Mixto                     |
| `felData.codigoExcepcion`    | `0` (NIT válido) o `1` (NIT inválido)           |
| `felData.numeroDocumento`    | NIT o CI del cliente                             |
| `felData.montoTotal`         | Total de la factura                              |
| `felData.montoTotalSujetoIva`| Monto sujeto a IVA                               |

**Importante:** Para el método de pago `86` (mixto), se requiere enviar `numeroTarjeta` y `montoGiftCard`. Este último reduce el monto sujeto a IVA.

**Ejemplo de envío (método mixto)**

```json
{
  "client_id": "9wdLgGRDbj",
  "number": "",
  "due_date": "",
  "terms": "",
  "public_notes": "",
  "private_notes": "",
  "is_amount_discount": true,
  "line_items": [
    {
      "quantity": 1,
      "cost": 1,
      "product_key": "PAGO OTROS SERVICIOS",
      "product_cost": 0,
      "notes": "PAGO OTROS SERVICIOS",
      "discount": 0,
      "is_amount_discount": true,
      "product_id": "olejV2BejN",
      "imei": "",
      "numeroSerie": ""
    }
  ],
  "entity_type": "invoice",
  "felData": {
    "codigoMetodoPago": "86",
    "codigoMoneda": "1",
    "sector_document_type_id": "1",
    "tipoCambio": 1,
    "facturaTicket": "34d48f02-a10b-489f-b991-c6a73864ac60",
    "extras": {
      "otros": "informacion extra"
    },
    "codigo_sucursal": "0",
    "codigoPuntoVenta": null,
    "descuentoAdicional": 0.02,
    "montoTotal": 0.98,
    "montoTotalMoneda": 0.98,
    "montoTotalSujetoIva": 0.48,
    "codigoTipoDocumentoIdentidad": "5",
    "complemento": "",
    "numeroDocumento": "2342342",
    "nombreRazonSocial": "marcus",
    "codigoExcepcion": 1,
    "numeroTarjeta": "4521000000002545",
    "montoGiftCard": "0.5"
  }
}
```

**Ejemplo de respuesta** (se omite por brevedad; incluye `cuf`, `sin_status`, `felData` completo, etc.).

---

### 4. Generar QR para Prefactura

**POST** `/api/v1/invoices/bulk`

Permite generar un código QR asociado a una prefactura. Al pagar el QR, la prefactura se emite automáticamente.

**Campos**

| Campo    | Descripción                                              |
|----------|----------------------------------------------------------|
| `ids`    | Lista de identificadores de prefacturas para facturar   |
| `action` | `bulk_generate_qr`                                       |
| `publish`| Siempre `false`                                          |

**Ejemplo de envío**

```json
{
  "ids": ["openY1r5d7"],
  "action": "bulk_generate_qr",
  "publish": false
}
```

**Respuesta**

```json
{
  "message": "QR enviado a terminal",
  "qr_id": 60790061,
  "qr_url": "https://emizor-felapp.s3.amazonaws.com/Qr-Image/2026-03-25/QR-Pago-Devicee5531f36-cc04-474a-a8df-d5a736a64f89.jpg"
}
```

---

### 5. Emitir Prefactura

**POST** `/api/v1/clientfel/invoices`

Convierte una prefactura en factura emitida.

**Ejemplo de envío**

```json
{
  "id_origin": "l9avY1gneG"
}
```

**Respuesta (ejemplo)**

```json
{
  "success": false,
  "msg": "La factura ya fue emitida"
}
```

---

### 6. Consultar Estado de Factura

**POST** `/api/v1/clientfel/invoices/status`

Verifica el estado de una factura (útil para facturas emitidas fuera de línea).

**Body**

```json
{
  "id_origin": "Jxbov1QYag"
}
```

**Respuesta**

```json
{
  "codigoEstado": 690,
  "estado": "VALIDA",
  "errores": "[{\"code\":2005,\"warning\":true,\"index_file\":null,\"description\":\"ADVERTENCIA: EL NIT DEL CLIENTE ENVIADO EN EL CAMPO NUMERO DE DOCUMENTO NO ES VALIDO Nit enviado 2342342 para codigo excepcion 1\"}]"
}
```

---

### 7. Listar Productos

**GET** `/api/v1/products?page=1&filter=`

Obtiene el catálogo de productos disponibles.

**Respuesta (ejemplo)**

```json
{
  "data": [
    {
      "id": "ELe30l9a69",
      "product_key": "Capital",
      "notes": "",
      "price": 1,
      "quantity": 1,
      "created_at": 1753199529,
      "updated_at": 1753199529,
      "archived_at": 0,
      "is_deleted": false,
      "felData": {
        "codigo_actividad_economica": "620901",
        "codigo": "cap",
        "codigo_producto": "pro-01",
        "codigo_producto_sin": "99100",
        "codigo_unidad": "58",
        "nombre_unidad": "UNIDAD (SERVICIOS)"
      }
    }
  ],
  "meta": {
    "pagination": { ... }
  }
}
```

---

## Flujo de Integración

### Caso 1: Emisión directa de factura

1. Verificar NIT del cliente (`/api/v1/clientfel/verify_nit`)
2. Crear cliente (`/api/v1/clients`)
3. Emitir factura (`/api/v1/invoices?should_emit=true`)

### Caso 2: Factura con QR

1. Verificar NIT del cliente
2. Crear cliente
3. Crear prefactura (`/api/v1/invoices?should_emit=false`)
4. Generar QR (`/api/v1/invoices/bulk`)

(Opcional) Consultar estado o emitir manualmente cuando el QR es pagado.

---

## Códigos de Estado Comunes

| Código | Descripción                          |
|--------|--------------------------------------|
| 200    | Petición exitosa                     |
| 400    | Error en la solicitud (formato, validación) |
| 422    | Datos inválidos                      |
| 500    | Error interno del servidor           |

---

## Notas Adicionales

- El `qr_id` generado se utiliza como identificador único para notificaciones de pago.
- Los campos `extras` pueden usarse para enviar información adicional en la factura.
- El campo `complemento` es requerido para documentos de identidad duplicados (ej. "A3").
- En métodos de pago mixtos (`86`), el campo `montoGiftCard` se resta del `montoTotalSujetoIva`.
- En métodos de pago que tengan en su descripción **Tarjeta** o **GiftCard**, el envío de los campos `numeroTarjeta` y `montoGiftCard` son requeridos.

---

### 8. Crear Productos

**POST** `/api/v1/products`

**Request**

```json
{
  "product_key": "Minerva",
  "notes": "Taladros",
  "price": "440",
  "felData": {
    "codigo_unidad": "57",
    "nombre_unidad": "UNIDAD (BIENES)",
    "codigo_actividad_economica": "620901",
    "codigo_producto_sin": "83151",
    "codigo": "Fa-052347",
    "codigo_producto": "Fa-052347"
  }
}
```

**Respuesta**

```json
{
  "data": {
    "id": "3YaOoMEaxq",
    "user_id": "k8mepPpeMy",
    "product_key": "Minerva",
    "notes": "Taladros",
    "price": 440,
    "created_at": 1776715686,
    "updated_at": 1776715686,
    "archived_at": 0,
    "is_deleted": false,
    "felData": { ... }
  }
}
```

---

### 9. Actualizar Productos

**PUT** `/api/v1/products/:id`

**Request** (similar al de creación, con los campos actualizados).

**Respuesta** (objeto actualizado).

---

### 10. Eliminar Productos

**DELETE** `/api/v1/products/:id`

Eliminación soft del producto.

**Respuesta** (objeto con `is_deleted: true` o similar).

---

### 11. Listar Paramétricas

**GET** `/api/v1/clientfel/parametricas/:parametrica`

**Parámetros posibles:**

- `actividades`
- `productos-sin`
- `unidades`
- `metodos-de-pago`
- `tipos-documento-de-identidad`

**Ejemplo**

```http
GET https://{app}.emizor.com/api/v1/clientfel/parametricas/tipos-documento-de-identidad
```

**Respuesta (ejemplo - tipos de documento)**

```json
{
  "success": false,
  "data": [
    {
      "id": 2,
      "codigo": "2",
      "descripcion": "CEX - CÉDULA DE IDENTIDAD DE EXTRANJERO",
      "created_at": null,
      "updated_at": "2021-12-01T16:59:07.000000Z"
    },
    {
      "id": 1,
      "codigo": "1",
      "descripcion": "CI - CÉDULA DE IDENTIDAD",
      "created_at": null,
      "updated_at": "2021-12-01T16:59:07.000000Z"
    },
    {
      "id": 5,
      "codigo": "5",
      "descripcion": "NIT - NÚMERO DE IDENTIFICACIÓN TRIBUTARIA",
      "created_at": null,
      "updated_at": "2021-12-01T16:59:07.000000Z"
    },
    {
      "id": 4,
      "codigo": "4",
      "descripcion": "OD - OTRO DOCUMENTO DE IDENTIDAD",
      "created_at": null,
      "updated_at": "2021-12-01T16:59:07.000000Z"
    },
    {
      "id": 3,
      "codigo": "3",
      "descripcion": "PAS - PASAPORTE",
      "created_at": null,
      "updated_at": "2021-12-01T16:59:07.000000Z"
    }
  ]
}
```

---

**Fin de la documentación.**  
Versión 1.2 – 24/02/2025