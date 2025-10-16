<?php
require_once 'config.php';

header('Content-Type: application/json');

$accion = $_POST['accion'] ?? '';

switch($accion) {
    case 'crear':
        crear_pedido();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}

function crear_pedido() {
    global $pdo;
    
    // Obtener datos del formulario
    $nombre = limpiar_input($_POST['nombre'] ?? '');
    $email = limpiar_input($_POST['email'] ?? '');
    $telefono = limpiar_input($_POST['telefono'] ?? '');
    $direccion = limpiar_input($_POST['direccion'] ?? '');
    $ciudad = limpiar_input($_POST['ciudad'] ?? '');
    $codigo_postal = limpiar_input($_POST['codigo_postal'] ?? '');
    
    // Validaciones
    if (empty($nombre) || empty($email) || empty($telefono) || empty($direccion) || empty($ciudad)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
        return;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email no válido']);
        return;
    }
    
    // Obtener carrito
    $usuario_id = obtener_usuario_id();
    $sesion_id = $_SESSION['sesion_id'];
    
    $sql = "
        SELECT ci.*, p.nombre, p.precio, p.stock
        FROM carrito_items ci
        INNER JOIN productos p ON ci.producto_id = p.id
        INNER JOIN carritos c ON ci.carrito_id = c.id
        WHERE " . ($usuario_id ? "c.usuario_id = ?" : "c.sesion_id = ? AND c.usuario_id IS NULL");
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_id ?? $sesion_id]);
    $items = $stmt->fetchAll();
    
    if (empty($items)) {
        echo json_encode(['success' => false, 'message' => 'El carrito está vacío']);
        return;
    }
    
    // Verificar stock y calcular total
    $total = 0;
    foreach ($items as $item) {
        if ($item['cantidad'] > $item['stock']) {
            echo json_encode(['success' => false, 'message' => 'Stock insuficiente para ' . $item['nombre']]);
            return;
        }
        $total += $item['cantidad'] * $item['precio_unitario'];
    }
    
    try {
        $pdo->beginTransaction();
        
        // Crear pedido
        $stmt = $pdo->prepare("
            INSERT INTO pedidos (usuario_id, nombre_cliente, email_cliente, telefono_cliente, 
                               direccion_envio, ciudad, codigo_postal, total) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $usuario_id,
            $nombre,
            $email,
            $telefono,
            $direccion,
            $ciudad,
            $codigo_postal,
            $total
        ]);
        
        $pedido_id = $pdo->lastInsertId();
        
        // Insertar detalles del pedido y actualizar stock
        $stmt_detalle = $pdo->prepare("
            INSERT INTO pedido_detalles (pedido_id, producto_id, nombre_producto, cantidad, precio_unitario, subtotal) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt_stock = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
        
        foreach ($items as $item) {
            $subtotal = $item['cantidad'] * $item['precio_unitario'];
            $stmt_detalle->execute([
                $pedido_id,
                $item['producto_id'],
                $item['nombre'],
                $item['cantidad'],
                $item['precio_unitario'],
                $subtotal
            ]);
            
            $stmt_stock->execute([$item['cantidad'], $item['producto_id']]);
        }
        
        // Vaciar carrito
        $carrito_id = $items[0]['carrito_id'];
        $stmt = $pdo->prepare("DELETE FROM carrito_items WHERE carrito_id = ?");
        $stmt->execute([$carrito_id]);
        
        $pdo->commit();
        
        // Enviar email con la factura
        enviar_factura($pedido_id, $email, $nombre, $items, $total, $direccion, $ciudad, $codigo_postal, $telefono);
        
        echo json_encode([
            'success' => true,
            'message' => 'Pedido creado exitosamente',
            'pedido_id' => $pedido_id
        ]);
        
    } catch(PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error al procesar el pedido']);
    }
}

function enviar_factura($pedido_id, $email, $nombre, $items, $total, $direccion, $ciudad, $codigo_postal, $telefono) {
    // Generar HTML de la factura
    $html_items = '';
    foreach ($items as $item) {
        $subtotal = $item['cantidad'] * $item['precio_unitario'];
        $html_items .= "
            <tr>
                <td style='padding: 10px; border-bottom: 1px solid #ddd;'>{$item['nombre']}</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'>{$item['cantidad']}</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: right;'>€" . number_format($item['precio_unitario'], 2) . "</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: right;'>€" . number_format($subtotal, 2) . "</td>
            </tr>
        ";
    }
    
    $mensaje = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
    </head>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h1 style='color: #2563eb; border-bottom: 3px solid #2563eb; padding-bottom: 10px;'>Factura de Pedido #$pedido_id</h1>
            
            <div style='background: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                <h2 style='margin-top: 0;'>Datos del Cliente</h2>
                <p><strong>Nombre:</strong> $nombre</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Teléfono:</strong> $telefono</p>
                <p><strong>Dirección:</strong> $direccion</p>
                <p><strong>Ciudad:</strong> $ciudad</p>
                <p><strong>Código Postal:</strong> $codigo_postal</p>
            </div>
            
            <h2>Detalles del Pedido</h2>
            <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                <thead>
                    <tr style='background: #2563eb; color: white;'>
                        <th style='padding: 10px; text-align: left;'>Producto</th>
                        <th style='padding: 10px; text-align: center;'>Cantidad</th>
                        <th style='padding: 10px; text-align: right;'>Precio</th>
                        <th style='padding: 10px; text-align: right;'>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    $html_items
                </tbody>
            </table>
            
            <div style='text-align: right; font-size: 18px; font-weight: bold; margin-top: 20px; padding: 15px; background: #f3f4f6; border-radius: 5px;'>
                Total: €" . number_format($total, 2) . "
            </div>
            
            <p style='margin-top: 30px; color: #666;'>Gracias por tu compra. Tu pedido será procesado en breve.</p>
        </div>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM . ">\r\n";
    
    mail($email, "Factura - Pedido #$pedido_id - " . SITE_NAME, $mensaje, $headers);
}
?>