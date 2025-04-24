<?php
session_start();
include('config.php');

$usuario = [];
$roles = [];
$dinero = $nivel = $edad = $sexo = $experiencia = $horas_conectado = $skin_imagen = $error = '';
$login_error = '';

$armas_nombres = [
    0 => 'Ninguna', 22 => 'Pistola', 23 => 'Silenciada', 24 => 'Desert Eagle',
    25 => 'Escopeta', 26 => 'Recortada', 27 => 'Combat Shotgun', 28 => 'Uzi',
    29 => 'MP5', 30 => 'AK-47', 31 => 'M4', 32 => 'Tec-9',
    33 => 'Rifle', 34 => 'Sniper', 38 => 'Minigun'
];

if (isset($_POST['login_user']) && isset($_POST['login_pass'])) {
    if ($_POST['login_user'] === 'admin' && $_POST['login_pass'] === '23') {
        $_SESSION['admin_logged'] = true;
    } else {
        $login_error = "Usuario o contraseña incorrectos.";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged']) {
    if (isset($_POST['edit_dinero']) && isset($_SESSION['admin_logged']) && isset($_POST['nombre_apellido'])) {
    $nombre_apellido = $_POST['nombre_apellido'];
    $query = "UPDATE cuentas SET 
        Dinero = '" . intval($_POST['edit_dinero']) . "',
        Nivel = '" . intval($_POST['edit_nivel']) . "',
        Vida = '" . intval($_POST['edit_vida']) . "',
        Chaleco = '" . intval($_POST['edit_chaleco']) . "',
        Sed = '" . intval($_POST['edit_sed']) . "',
        Hambre = '" . intval($_POST['edit_hambre']) . "',
        Alcohol = '" . intval($_POST['edit_alcohol']) . "'
        WHERE Nombre_Apellido = '$nombre_apellido'";
    $conn->query($query);
}

if (isset($_POST['nombre_apellido'])) {
        $nombre_apellido = $_POST['nombre_apellido'];
        $query = "SELECT * FROM cuentas WHERE Nombre_Apellido = '$nombre_apellido'";
        $resultado = $conn->query($query);

        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
            $dinero = $usuario['Dinero'];
            $nivel = $usuario['Nivel'];
            $edad = $usuario['Edad'];
            $sexo = $usuario['Sexo'];
            $experiencia = $usuario['Experiencia'];
            $horas_conectado = $usuario['HorasConectado'];
            $skin = isset($usuario['Skin']) ? $usuario['Skin'] : 0;
            $skin_imagen = "https://assets.open.mp/assets/images/skins/$skin.png";

            if ($usuario['Encargado_1']) $roles[] = "STAFF";
            if ($usuario['Encargado_2']) $roles[] = "BANDAS";
            if ($usuario['Encargado_3']) $roles[] = "BAN";
            if ($usuario['Encargado_4']) $roles[] = "ROL";
            if ($usuario['Encargado_5']) $roles[] = "EVENTOS";
            if ($usuario['Encargado_6']) $roles[] = "AYUDANTES";
            if ($usuario['Encargado_7']) $roles[] = "OTRO";
        } else {
            $error = "Usuario no encontrado.";
        }
    }
}

$usuarios_lista = [];
$query_usuarios = "SELECT Nombre_Apellido FROM cuentas";
$resultado_usuarios = $conn->query($query_usuarios);
if ($resultado_usuarios && $resultado_usuarios->num_rows > 0) {
    while ($usuario_item = $resultado_usuarios->fetch_assoc()) {
        $usuarios_lista[] = $usuario_item['Nombre_Apellido'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary-color: #4a5568;
            --background-color: rgba(255, 255, 255, 0.1);
            --panel-bg: rgba(45, 55, 72, 0.3);
            --text-color: #ffffff;
            --stat-bg: rgba(45, 55, 72, 0.95);
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: url('https://images.unsplash.com/photo-1511497584788-876760111969') center/cover fixed;
            color: var(--text-color);
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
            margin: 50px auto;
            background: var(--panel-bg);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        .theme-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .theme-controls label {
            display: block;
            margin: 10px 0;
        }
        .theme-controls input {
            margin-left: 10px;
        }
        .input-group input {
            background: rgba(255,255,255,0.9);
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }
        .input-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(232,67,147,0.3);
        }
        button {
            background: var(--primary-color) !important;
            transition: all 0.3s ease !important;
            border: none !important;
            padding: 10px 20px !important;
            color: white !important;
            border-radius: 8px !important;
            backdrop-filter: blur(5px);
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 85, 104, 0.4);
            background: #5a6778 !important;
        }
        button:active {
            transform: scale(0.98);
            box-shadow: 0 2px 8px rgba(74, 85, 104, 0.3);
        }
        .input-group, .actions {
            margin: 15px 0;
            text-align: center;
        }
        input, button {
            padding: 10px;
            margin: 5px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
        }
        button {
            background-color: #e84393;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #d63031;
        }
        .user-data {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            padding: 20px;
            background: var(--panel-bg);
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stat-item {
            background: var(--stat-bg);
            padding: 15px;
            border-radius: 10px;
            transition: transform 0.2s;
        }
        .stat-item:hover {
            transform: translateY(-2px);
        }
        .stat-item p {
            margin: 0;
            font-size: 0.95em;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .skin {
            text-align: center;
            padding: 20px;
            background: var(--panel-bg);
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .skin img {
            width: 250px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.4);
        }
    <style>
        /* Estilos para el botón flotante y menú */
        #floating-button, #close-button {
            position: fixed;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            text-align: center;
            line-height: 50px;
            font-size: 20px;
            cursor: pointer;
            z-index: 1000;
            transition: transform 0.3s, background 0.3s;
        }

        #floating-button {
            bottom: 20px;
            right: 20px;
            background: var(--primary-color);
            color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }

        #close-button {
            bottom: 20px;
            right: 80px;
            background: #ff6b6b;
            color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            display: none;
        }

        #floating-button:hover, #close-button:hover {
            transform: scale(1.1);
        }

        #edit-menu {
            display: none;
            position: fixed;
            left: 50%;
            bottom: 20px;
            transform: translateX(-50%);
            background: rgba(45, 55, 72, 0.98);
            padding: 25px;
            border-radius: 15px;
            color: white;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            z-index: 999;
            min-width: 80%;
            max-width: 1200px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .edit-fields-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .edit-field {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .edit-field:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .edit-field input {
            width: 100%;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid transparent;
            padding: 8px;
            border-radius: 5px;
            margin-top: 5px;
            transition: all 0.3s ease;
        }

        .edit-field input:focus {
            border-color: #e84393;
            outline: none;
            box-shadow: 0 0 5px rgba(232,67,147,0.3);
        }

        .save-button {
            background: #e84393 !important;
            width: 100%;
            padding: 15px !important;
            font-size: 1.1em !important;
            margin-top: 20px;
            transition: all 0.3s ease !important;
        }

        .save-button:hover {
            background: #d63031 !important;
            transform: translateY(-2px);
        }

        #edit-menu h3 {
            margin: 0 0 20px 0;
            color: var(--primary-color);
            text-align: center;
            font-size: 1.3em;
        }

        .edit-field {
            margin: 15px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .edit-field label {
            font-weight: bold;
            color: #555;
        }

        .edit-field input {
            width: 120px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        .edit-field input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(232,67,147,0.2);
        }

        #edit-form button {
            margin-top: 20px;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
            font-weight: bold;
        }

        #edit-form button:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<div id="floating-button">⚙️</div>
<div id="close-button">❌</div>
<div id="edit-menu">
    <h3>✏️ Editar Jugador</h3>
    <form id="edit-form" method="POST" onsubmit="return validateForm()">
        <input type="hidden" name="nombre_apellido" value="<?= htmlspecialchars($usuario['Nombre_Apellido'] ?? '') ?>">
        <div class="edit-fields-container">
            <div class="edit-field">
                <label>💰 Dinero: </label>
                <input type="number" name="edit_dinero" value="<?= $usuario['Dinero'] ?? '' ?>">
            </div>
            <div class="edit-field">
                <label>⭐ Nivel: </label>
                <input type="number" name="edit_nivel" value="<?= $usuario['Nivel'] ?? '' ?>">
            </div>
            <div class="edit-field">
                <label>❤️ Vida: </label>
                <input type="number" name="edit_vida" min="0" max="100" value="<?= $usuario['Vida'] ?? '' ?>">
            </div>
            <div class="edit-field">
                <label>🛡️ Chaleco: </label>
                <input type="number" name="edit_chaleco" min="0" max="100" value="<?= $usuario['Chaleco'] ?? '' ?>">
            </div>
            <div class="edit-field">
                <label>💧 Sed: </label>
                <input type="number" name="edit_sed" min="0" max="100" value="<?= $usuario['Sed'] ?? '' ?>">
            </div>
            <div class="edit-field">
                <label>🍗 Hambre: </label>
                <input type="number" name="edit_hambre" min="0" max="100" value="<?= $usuario['Hambre'] ?? '' ?>">
            </div>
            <div class="edit-field">
                <label>🍷 Alcohol: </label>
                <input type="number" name="edit_alcohol" min="0" max="100" value="<?= $usuario['Alcohol'] ?? '' ?>">
            </div>
        </div>
        <button type="submit" class="save-button">Guardar Cambios</button>
    </form>
</div>

<script>
const floatingButton = document.getElementById('floating-button');
const closeButton = document.getElementById('close-button');
const editMenu = document.getElementById('edit-menu');
let isDragging = false;
let currentX;
let currentY;
let initialX;
let initialY;
let xOffset = 0;
let yOffset = 0;

// Hacer el menú movible
editMenu.addEventListener('mousedown', dragStart);
document.addEventListener('mousemove', drag);
document.addEventListener('mouseup', dragEnd);

function dragStart(e) {
    if (e.target === editMenu || e.target.closest('h3')) {
        isDragging = true;
        initialX = e.clientX - xOffset;
        initialY = e.clientY - yOffset;
    }
}

function drag(e) {
    if (isDragging) {
        e.preventDefault();
        currentX = e.clientX - initialX;
        currentY = e.clientY - initialY;

        xOffset = currentX;
        yOffset = currentY;

        setTranslate(currentX, currentY, editMenu);
    }
}

function dragEnd(e) {
    initialX = currentX;
    initialY = currentY;
    isDragging = false;
}

function setTranslate(xPos, yPos, el) {
    el.style.transform = `translate3d(${xPos}px, ${yPos}px, 0)`;
}

// Mostrar/ocultar menú y botón de cerrar
floatingButton.addEventListener('click', (e) => {
    editMenu.style.display = 'block';
    closeButton.style.display = 'block';
});

closeButton.addEventListener('click', (e) => {
    editMenu.style.display = 'none';
    closeButton.style.display = 'none';
});

// Prevenir que los clicks en inputs inicien el arrastre
editMenu.querySelectorAll('input').forEach(input => {
    input.addEventListener('mousedown', (e) => e.stopPropagation());
});
</script>

<script>
function validateForm() {
    const form = document.getElementById('edit-form');
    const formData = new FormData(form);
    
    // Mantener valores existentes si los campos están vacíos
    for (let pair of formData.entries()) {
        if (pair[1] === '') {
            const input = form.querySelector(`[name="${pair[0]}"]`);
            if (input && input.value) {
                input.value = input.getAttribute('value');
            }
        }
    }
    
    return true;
}
</script>

<div class="container">
    <h1>Panel de Administración</h1>



    <?php if (!isset($_SESSION['admin_logged'])): ?>
        <form method="POST">
            <div class="input-group">
                <input type="text" name="login_user" placeholder="Usuario" required>
                <input type="password" name="login_pass" placeholder="Contraseña" required>
                <button type="submit">Iniciar Sesión</button>
            </div>
            <?php if ($login_error): ?><p style="color: red;"><?= $login_error ?></p><?php endif; ?>
        </form>
    <?php else: ?>
        <form method="POST">
            <div class="input-group">
                <input type="text" id="nombre_apellido" name="nombre_apellido" placeholder="Nombre_Apellido" required>
                <button type="submit">Consultar</button>
                <button type="button" onclick="toggleUsuarios()">Mostrar Usuarios</button>
            </div>
            <div class="actions">
        <div id="usuariosPanel" style="display: none;">
            <ul style="list-style: none; padding: 10px; background: rgba(45, 55, 72, 0.9); border: 1px solid rgba(255,255,255,0.2); max-height: 200px; overflow-y: auto; color: white; border-radius: 8px;">
<script>
function toggleUsuarios() {
    var panel = document.getElementById('usuariosPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}
</script>
                <?php foreach ($usuarios_lista as $usuario_item): ?>
                    <li onclick="document.getElementById('nombre_apellido').value = '<?= $usuario_item ?>'"><?= $usuario_item ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <a href="?logout=true" style="margin-left: 15px; color: red;">Cerrar Sesión</a>

        <?php if ($error): ?>
            <p style="color:red"><?= $error ?></p>
        <?php elseif ($dinero !== ''): ?>
            <div class="user-data">
                <div class="stats">
                    <div class="stat-item">
                        <p>💰 Dinero: <span>$<?= number_format($dinero) ?></span></p>
                    <p>⭐ Nivel: <span><?= $nivel ?></span></p>
                    <p>🎂 Edad: <span><?= $edad ?> años</span></p>
                    <p>👤 Sexo: <span><?= ($sexo == 1 ? 'Masculino' : 'Femenino') ?></span></p>
                    <p>📈 Experiencia: <span><?= $experiencia ?> XP</span></p>
                    <p>⏰ Horas Conectado: <span><?= $horas_conectado ?>h</span></p>
                    <p>📧 Email: <span><?= $usuario['Email'] ?></span></p>
                    <p>🟢 Estado: <span style="color: <?= ($usuario['Conectado'] ? 'green' : 'red') ?>"><?= ($usuario['Conectado'] ? 'En línea' : 'Desconectado') ?></span></p>
                    <p>⚙️ Encargos: <span><?= implode(', ', $roles) ?: 'Ninguno' ?></span></p>
                    <p>🗓️ Registro: <span><?= $usuario['FechaRegistro'] ?></span></p>
                    <p>⏳ Última Conexión: <span><?= $usuario['UltimaConexion'] ?></span></p>
                    <p>❤️ Vida: <span><?= $usuario['Vida'] ?>%</span></p>
                    <p>🛡️ Chaleco: <span><?= $usuario['Chaleco'] ?>%</span></p>
                    <p>💧 Sed: <span><?= $usuario['Sed'] ?></span></p>
                    <p>🍗 Hambre: <span><?= $usuario['Hambre'] ?></span></p>
                    <p>🍷 Alcohol: <span><?= $usuario['Alcohol'] ?></span></p>
                    <p>💪 Fuerza: <span><?= $usuario['Fuerza'] ?></span></p>
                    <p>🏦 Banco: <span>$<?= number_format($usuario['Banco']) ?></span></p>
                    <p>🪙 Coins: <span><?= $usuario['Coins'] ?></span></p>
                    <p>🌟 Nivel VIP: <span><?= $usuario['NivelVIP'] ?></span></p>
                    <p>🎨 Color VIP: <span>#<?= strtoupper(dechex($usuario['ColorVIP'])) ?></span></p>
                    <p>📅 Día VIP: <span><?= $usuario['DiaVIP'] == -1 ? 'Sin VIP' : $usuario['DiaVIP'] ?></span></p>
                    <p>🗓️ Mes VIP: <span><?= $usuario['MesVIP'] == -1 ? 'Sin VIP' : $usuario['MesVIP'] ?></span></p>
                    <p>🔫 Armas Equipadas:</p>
                    <ul>
                        <?php for ($i = 0; $i <= 11; $i++): 
                            $id_arma = $usuario["Arma_$i"];
                            if ($id_arma > 0): ?>
                            <li><?= $armas_nombres[$id_arma] ?? 'Desconocida' ?> [<?= $id_arma ?>]</li>
                            <?php endif;
                        endfor; ?>
                    </ul>
                </div>
                <div class="skin">
                    <p><strong><?= $usuario['Nombre_Apellido'] ?></strong></p>
                    <img src="<?= $skin_imagen ?>" alt="Imagen del skin de <?= $usuario['Nombre_Apellido'] ?>">
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>