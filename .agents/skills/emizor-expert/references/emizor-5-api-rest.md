# Documentación Técnica – API REST Emizor 5

## INFORMACIÓN

| NOMBRE              | CARGO                  | Versión | FECHA      |
|---------------------|------------------------|---------|------------|
| Denis Silisqui A.   | Responsable de Desarrollo | 1.0     | 24/02/2025 |

---

## Contenido

- [Introducción](#introducción)
- [Autenticación](#autenticación)
- [Obtención del token](#obtención-del-token)
- [Headers requeridos](#headers-requeridos)
- [Base URL (desarrollo)](#base-url-desarrollo)
- [Endpoints](#endpoints)
  - [1. Verificar NIT](#1-verificar-nit)
  - [2. Crear Cliente](#2-crear-cliente)
  - [3. Emitir Factura o Prefactura](#3-emitir-factura-o-prefactura)
  - [4. Generar QR para Prefactura](#4-generar-qr-para-prefactura)
  - [5. Emitir Prefactura](#5-emitir-prefactura)
  - [6. Consultar Estado de Factura](#6-consultar-estado-de-factura)
  - [7. Listar Productos](#7-listar-productos)
- [Flujo de Integración](#flujo-de-integración)
  - [Caso 1: Emisión directa de factura](#caso-1-emisión-directa-de-factura)
  - [Caso 2: Factura con QR](#caso-2-factura-con-qr)
- [Códigos de Estado Comunes](#códigos-de-estado-comunes)
- [Notas Adicionales](#notas-adicionales)

---

## Introducción

Esta documentación describe los servicios disponibles en la **API REST de Emizor 5**, plataforma de facturación electrónica para Bolivia.

La API permite la creación de clientes, emisión de facturas, generación de códigos QR y consulta de estados ante el SIN (Servicio de Impuestos Nacionales).

---

## Autenticación

Para consumir los servicios es necesario contar con un `X-Api-Token`.

### Obtención del token

1. Ingresa a la plataforma Emizor con tu usuario y contraseña.
2. Dirígete a: **Configuración → Gestión de cuentas → Integraciones → API Tokens**
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
https://{your-env}.emizor.com
```

---

## Endpoints

### 1. Verificar NIT

**GET** `/api/v1/clientfel/verify_nit/{nit}`

Valida si un NIT existe ante el SIN. Se usa para determinar el valor del campo `codigoExcepcion` en la factura.

**Parámetro**

| Parámetro | Tipo   | Descripción                  |
|-----------|--------|------------------------------|
| `nit`     | string | NIT del cliente a verificar  |

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

> **Nota:** Si `success` es `true`, el `codigoExcepcion` debe ser `0`; en caso contrario, debe ser `1`.

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

**Ejemplo de respuesta** (parcial)

```json
{
  "data": {
    "id": "9wdLgGRDbj",
    "name": "Denis",
    "phone": "71558926",
    "number": "6638075",
    "felData": {
      "type_document_id": "1",
      "business_name": "Silisqui",
      "document_number": "6638075"
    },
    "contacts": [ ... ]
  }
}
```

---

### 3. Emitir Factura o Prefactura

**POST** `/api/v1/invoices?should_emit=true&paid=false&send_mail=true`

**Parámetros de consulta**

| Parámetro     | Descripción                          |
|---------------|--------------------------------------|
| `should_emit` | `true` = emisión directa, `false` = prefactura |
| `paid`        | Indica si está pagada                |
| `send_mail`   | Enviar factura por correo            |

**Campos clave en el body**

| Campo                          | Descripción                                      |
|--------------------------------|--------------------------------------------------|
| `client_id`                    | ID del cliente obtenido en la creación           |
| `line_items[].product_id`      | ID del producto (obtenido vía API de productos)  |
| `felData.codigoMetodoPago`     | `5` = Efectivo, `86` = Mixto                     |
| `felData.codigoExcepcion`      | `0` (NIT válido) o `1` (NIT inválido)           |
| `felData.numeroDocumento`      | NIT o CI del cliente                             |
| `felData.montoTotal`           | Total de la factura                              |
| `felData.montoTotalSujetoIva`  | Monto sujeto a IVA                               |

> **Importante:** Para el método de pago `86` (mixto), se requiere enviar `numeroTarjeta` y `montoGiftCard`.

**Ejemplo de envío (método mixto)**

```json
{
  "client_id": "9wdLgGRDbj",
  "line_items": [
    {
      "quantity": 1,
      "cost": 1,
      "product_key": "PAGO OTROS SERVICIOS",
      "product_id": "olejV2BejN",
      "notes": "PAGO OTROS SERVICIOS"
    }
  ],
  "entity_type": "invoice",
  "felData": {
    "codigoMetodoPago": "86",
    "codigoMoneda": "1",
    "sector_document_type_id": "1",
    "tipoCambio": 1,
    "facturaTicket": "34d48f02-a10b-489f-b991-c6a73864ac60",
    "codigo_sucursal": "0",
    "descuentoAdicional": 0.02,
    "montoTotal": 0.98,
    "montoTotalSujetoIva": 0.48,
    "codigoTipoDocumentoIdentidad": "5",
    "numeroDocumento": "2342342",
    "nombreRazonSocial": "marcus",
    "codigoExcepcion": 1,
    "numeroTarjeta": "4521000000002545",
    "montoGiftCard": "0.5"
  }
}
```

**Ejemplo de respuesta** (exitosa)

```json
{
  "data": {
    "id": "X7axVgvldy",
    "cuf": "19F6D082E4891AA0524E7D52CB2DD0C119D2A35D89647854A588AAF74",
    "sin_status": "VALIDA",
    "felData": { ... }
  }
}
```

---

### 4. Generar QR para Prefactura

**POST** `/api/v1/invoices/bulk`

Permite generar un código QR asociado a una prefactura. Al pagar el QR, la prefactura se emite automáticamente.

**Campos**

| Campo    | Descripción                                      |
|----------|--------------------------------------------------|
| `ids`    | Lista de identificadores de prefacturas          |
| `action` | `bulk_generate_qr`                               |
| `publish`| Siempre `false`                                  |

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
  "qr_url": "https://emizor-felapp.s3.amazonaws.com/..."
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

**Respuesta**

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
  "errores": "[{\"code\":2005,\"warning\":true,...}]"
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
      "felData": {
        "codigo_actividad_economica": "620901",
        "codigo_producto_sin": "99100"
      }
    }
  ],
  "meta": { ... }
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

---

## Códigos de Estado Comunes

| Código | Descripción                              |
|--------|------------------------------------------|
| 200    | Petición exitosa                         |
| 400    | Error en la solicitud (formato, validación) |
| 422    | Datos inválidos                          |
| 500    | Error interno del servidor               |

---

## Notas Adicionales

- El `qr_id` generado se utiliza como identificador único para notificaciones de pago.
- Los campos `extras` pueden usarse para enviar información adicional en la factura.
- El campo `complemento` es requerido para documentos de identidad duplicados (ej. "A3").
- En métodos de pago mixtos (`86`), el campo `montoGiftCard` se resta del `montoTotalSujetoIva`.
- En métodos de pago que incluyan **Tarjeta** o **GiftCard**, los campos `numeroTarjeta` y `montoGiftCard` son obligatorios.

---

**Base URL de desarrollo:** `https://{your-env}.emizor.com`

**Versión de la documentación:** 1.0 (24/02/2025)
```