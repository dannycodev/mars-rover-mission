<template>
    <div class="rover-app">
      <h1 style="color:black !important; margin: 0px;">Mars Rover Control</h1>
      
      <div class="control-panel">
        <div class="position-info">
          <p>Posición: ({{ roverPosition.x }}, {{ roverPosition.y }}) - Dirección: {{ roverDirection }}</p>
        </div>
        
        <div class="command-input">
          <input 
            v-model="commandInput" 
            placeholder="Comandos (F: adelante, R: derecha, L: izquierda)" 
            :disabled="isProcessing"
          />
          <button @click="sendCommands" :disabled="isProcessing">Enviar</button>
          <button @click="resetRover">Reiniciar Rover</button>
        </div>
        
        <div v-if="message" class="message" :class="{ 
          'success': !message.includes('Error') && !message.includes('Obstáculo'), 
          'error': message.includes('Error') || message.includes('Obstáculo') 
        }">
          {{ message }}
        </div>
      </div>
      
      <div class="grid-container">
        <div class="grid">
          <div v-for="(row, y) in visibleGrid" :key="`row-${y}`" class="grid-row">
            <div 
              v-for="(cell, x) in row" 
              :key="`cell-${x}-${y}`" 
              class="grid-cell"
              :class="{
                'out-of-bounds': cell.outOfBounds,
                'has-obstacle': cell.hasObstacle
              }"
            >
              <div v-if="cell.hasRover" class="rover" :style="{ transform: `rotate(${roverRotation})` }">
                ▲
              </div>
              <template v-else-if="cell.hasObstacle">■</template>
              <template v-else>{{ cell.outOfBounds ? '' : `${cell.x},${cell.y}` }}</template>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, computed } from 'vue';
  import axios from 'axios';
  
  // Estado del rover y la cuadrícula
  const gridSize = ref(20);
  const displaySize = ref(20); 
  
  const roverPosition = ref({ x: 0, y: 0 });
  const roverDirection = ref('N');
  const obstacles = ref([]);
  const commandInput = ref('');
  const message = ref('');
  const isProcessing = ref(false);
  
  // Generar algunos obstáculos aleatorios para demostración
  onMounted(() => {
    generateRandomObstacles(10);
    message.value = '';
  });
  
  const generateRandomObstacles = (count) => {
    // Para una cuadrícula pequeña, generamos menos obstáculos
    // para evitar que esté demasiado lleno
    const actualCount = Math.min(count, Math.floor(gridSize.value * gridSize.value * 0.15));
    const newObstacles = [];
    
    // Usamos un Set para rastrear posiciones ya ocupadas
    const occupiedPositions = new Set();
    // Añadimos la posición del rover
    occupiedPositions.add(`${roverPosition.value.x},${roverPosition.value.y}`);
    
    // Distribuir obstáculos de manera más uniforme
    for (let i = 0; i < actualCount; i++) {
      let x, y;
      let posKey;
      let attempts = 0;
      
      // Intentar encontrar una posición vacía
      do {
        x = Math.floor(Math.random() * gridSize.value);
        y = Math.floor(Math.random() * gridSize.value);
        posKey = `${x},${y}`;
        attempts++;
        
        // Evitar bucles infinitos
        if (attempts > 50) break;
      } while (occupiedPositions.has(posKey));
      
      // Si encontramos una posición no ocupada
      if (!occupiedPositions.has(posKey)) {
        newObstacles.push({ x, y });
        occupiedPositions.add(posKey);
      }
    }
    
    obstacles.value = newObstacles;
  };
  
  // Calcular la porción visible del grid
  const visibleGrid = computed(() => {
    const grid = [];
    for (let y = 0; y < gridSize.value; y++) {
      const row = [];
      for (let x = 0; x < gridSize.value; x++) {
        // Verificar si hay un obstáculo en esta posición
        const hasObstacle = obstacles.value.some(obs => 
          obs.x === x && obs.y === y);
        
        // Verificar si el rover está en esta posición
        const hasRover = roverPosition.value.x === x && 
                         roverPosition.value.y === y;
        
        row.push({
          x: x,
          y: y,
          hasRover,
          hasObstacle,
          outOfBounds: false 
        });
      }
      grid.push(row);
    }
    return grid;
  });
  
  // Calcular la rotación del rover basada en su dirección
  const roverRotation = computed(() => {
    switch (roverDirection.value) {
      case 'N': return '0deg';
      case 'E': return '90deg';
      case 'S': return '180deg';
      case 'W': return '270deg';
      default: return '0deg';
    }
  });
  
  // Enviar comandos al backend
  const sendCommands = async () => {
    if (!commandInput.value || isProcessing.value) return;
    
    isProcessing.value = true;
    message.value = '';
    
    try {
      // Enviar al backend
      const response = await axios.post('http://localhost:8000/api/rover/move', {
        commands: commandInput.value,
        initialPosition: { 
          x: roverPosition.value.x, 
          y: roverPosition.value.y, 
          direction: roverDirection.value 
        },
        obstacles: obstacles.value
      });
      
      // Actualizar estado con la respuesta
      const result = response.data;
      roverPosition.value = { x: result.position.x, y: result.position.y };
      roverDirection.value = result.position.direction;
      
      // Mensaje
      if (result.success) {
        message.value = 'Movimiento correcto';
      } else {
        message.value = `Obstáculo en (${result.obstaclePosition.x}, ${result.obstaclePosition.y})`;
      }
      
      // Limpiar input
      commandInput.value = '';
      
    } catch (error) {
      message.value = 'Error: ' + error.message;
    } finally {
      isProcessing.value = false;
    }
  };
  
  // Reiniciar la posición del rover
  const resetRover = () => {
    roverPosition.value = { x: 0, y: 0 };
    roverDirection.value = 'N';
    message.value = '';
    // Generar nuevos obstáculos
    generateRandomObstacles(10);
  };
  </script>
  
  <style scoped>
  .rover-app {
    max-width: 900px;
    margin: 0 auto;
    /* padding: 20px; */
    font-family: Arial, sans-serif;
  }
  
  .control-panel {
    /* margin-bottom: 20px; */
    padding: 15px;
    background-color: #f5f5f5;
    border-radius: 8px;
  }
  
  .command-input {
    display: flex;
    gap: 10px;
    margin: 15px 0;
  }
  
  .command-input input {
    flex: 1;
    padding: 8px;
    font-size: 16px;
  }
  
  button {
    padding: 8px 16px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  
  button:disabled {
    background-color: #cccccc;
  }
  
  .message {
    padding: 10px;
    margin-top: 10px;
    border-radius: 4px;
    font-weight: bold;
    transition: all 0.3s ease;
    text-align: center;
  }
  
  .message.success {
    background-color: #4CAF50;
    color: white;
  }
  
  .message.error {
    background-color: #F44336;
    color: white;
  }
  
  .grid-container {
    overflow: auto;
    max-width: 100%;
    margin-bottom: 20px;
  }
  
  .grid {
    display: flex;
    flex-direction: column;
    border: 2px solid #333;
    display: inline-block;
  }
  
  .grid-row {
    display: flex;
  }
  
  .grid-cell {
    width: 30px;
    height: 30px;
    border: 1px solid #ddd;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    position: relative;
    color: #000;
  }
  
  .grid-cell.out-of-bounds {
    background-color: #f0f0f0;
  }
  
  .grid-cell.has-obstacle {
    background-color: #f19239;
    font-size: 16px;
  }
  
  .rover {
    font-size: 18px;
    transition: transform 0.3s ease;
  }
  
  .position-info {
    color: #000000;
    font-weight: bold;
  }
  </style>