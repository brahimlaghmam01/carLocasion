<template>
  <div class="hero">
    <div ref="container" class="viewer"></div>

    <div class="hero-text">
      <p class="hero-label">Premium Fleet</p>
      <h1 class="hero-title">{{ currentCar.name }}</h1>
      <p class="hero-sub">{{ currentCar.tagline }}</p>
      <div class="hero-stats">
        <div class="stat">
          <span class="stat-value">{{ currentCar.price }}</span>
          <span class="stat-label">/ day</span>
        </div>
        <div class="divider"></div>
        <div class="stat">
          <span class="stat-value">{{ currentCar.seats }}</span>
          <span class="stat-label">seats</span>
        </div>
        <div class="divider"></div>
        <div class="stat">
          <span class="stat-value">{{ currentCar.transmission }}</span>
          <span class="stat-label">trans.</span>
        </div>
      </div>
      <button class="cta" @click="goToFleet">
  Reserve Now →
</button>
    </div>

    <button class="left" @click="prev">‹</button>
    <button class="right" @click="next">›</button>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import * as THREE from "three"
import { onMounted, ref, onBeforeUnmount, computed } from "vue"
import { GLTFLoader } from "three/examples/jsm/loaders/GLTFLoader"

const container = ref(null)
const index = ref(0)

const cars = [
  {
    path: "/models/car1.glb",
    name: "Honda Civic Type R 2024",
    tagline: "Track-bred performance.",
    price: "$149",
    seats: "5",
    transmission: "Manual",
  },
{
  path: "/models/car2.glb",
  name: "Mercedes AMG GT",
  tagline: "Power meets elegance.",
  price: "$219",
  seats: "2",
  transmission: "Auto",
},
  {
    path: "/models/car3.glb",
    name: "BMW M3 Sedan",
    tagline: "Everyday performance.",
    price: "$179",
    seats: "5",
    transmission: "Auto",
  },
  {
    path: "/models/car4.glb",
    name: "BMW M4 Competition",
    tagline: "Aggressive luxury.",
    price: "$209",
    seats: "4",
    transmission: "Auto",
  },
  {
    path: "/models/car5.glb",
    name: "BMW 1M",
    tagline: "Compact driving excitement.",
    price: "$169",
    seats: "4",
    transmission: "Manual",
  },
  {
    path: "/models/car6.glb",
    name: "Porsche 911",
    tagline: "Born for the open road.",
    price: "$199",
    seats: "4",
    transmission: "Auto",
  },
  {
    path: "/models/car7.glb",
    name: "Mercedes AMG",
    tagline: "Luxury redefined.",
    price: "$219",
    seats: "5",
    transmission: "Auto",
  },
]

const currentCar = computed(() => cars[index.value])

let scene, camera, renderer, loader
let currentModel = null
let autoTimer
let animationId
function goToFleet() {
  router.visit('/fleet')
}
function frameModel(model, camera) {
  const box = new THREE.Box3().setFromObject(model)
  const center = new THREE.Vector3()
  const size = new THREE.Vector3()
  box.getCenter(center)
  box.getSize(size)
  model.position.sub(center)
  model.position.y += size.y / 2
  const box2 = new THREE.Box3().setFromObject(model)
  const center2 = new THREE.Vector3()
  box2.getCenter(center2)
  const maxSize = Math.max(size.x, size.y, size.z)
  const fitHeightDistance = maxSize / (2 * Math.tan(((camera.fov / 2) * Math.PI) / 180))
  const fitWidthDistance = fitHeightDistance / camera.aspect
  const distance = Math.max(fitHeightDistance, fitWidthDistance)
  camera.position.set(0, center2.y, distance * 0.7)
  camera.lookAt(center2)
  camera.updateProjectionMatrix()
}

function init() {
  scene = new THREE.Scene()
  camera = new THREE.PerspectiveCamera(50, window.innerWidth / 500, 0.1, 1000)
  renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true })
  renderer.setSize(window.innerWidth, 500)
  renderer.setPixelRatio(window.devicePixelRatio)
  container.value.appendChild(renderer.domElement)
  loader = new GLTFLoader()
  scene.add(new THREE.HemisphereLight(0xffffff, 0x444444, 1.5))
  const dirLight = new THREE.DirectionalLight(0xffffff, 2)
  dirLight.position.set(5, 5, 5)
  scene.add(dirLight)
  loadCar(index.value)
  animate()
}

function loadCar(i) {
  if (currentModel) {
    scene.remove(currentModel)
    currentModel.traverse((obj) => {
      if (obj.geometry) obj.geometry.dispose?.()
      if (obj.material) obj.material.dispose?.()
    })
  }
  loader.load(cars[i].path, (gltf) => {
    currentModel = gltf.scene
    currentModel.position.set(0, 0, 0)
    currentModel.rotation.set(0, 0, 0)
    currentModel.scale.set(1, 1, 1)
    scene.add(currentModel)
    currentModel.updateMatrixWorld(true)
    frameModel(currentModel, camera)
  })
}

function next() {
  index.value = (index.value + 1) % cars.length
  loadCar(index.value)
  resetAuto()
}

function prev() {
  index.value = (index.value - 1 + cars.length) % cars.length
  loadCar(index.value)
  resetAuto()
}

function autoRotate() {
  autoTimer = setInterval(() => next(), 5000)
}

function resetAuto() {
  clearInterval(autoTimer)
  autoRotate()
}

function animate() {
  animationId = requestAnimationFrame(animate)
  if (currentModel) currentModel.rotation.y += 0.035
  renderer.render(scene, camera)
}

function onResize() {
  camera.aspect = window.innerWidth / 500
  camera.updateProjectionMatrix()
  renderer.setSize(window.innerWidth, 500)
}

onMounted(() => {
  init()
  autoRotate()
  window.addEventListener("resize", onResize)
})

onBeforeUnmount(() => {
  clearInterval(autoTimer)
  cancelAnimationFrame(animationId)
  window.removeEventListener("resize", onResize)
})
</script>

<style scoped>
.hero {
  position: relative;
  height: 600px;
  overflow: hidden;
  background: #0d0d0d;
  border-top: 1px solid #2a2a2a;
  border-bottom: 1px solid #2a2a2a;
}

.hero::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 16px;
  background: linear-gradient(90deg, transparent, #f0a500, transparent);
  z-index: 20;
}

.hero::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 16px;
  background: linear-gradient(90deg, transparent, #f0a500, transparent);
  z-index: 20;
}
.viewer {
  width: 100%;
  height: 600px;
}

.hero-text {
  position: absolute;
  top: 50%;
  left: 6%;
  transform: translateY(-50%);
  z-index: 10;
  color: #cecece;
  pointer-events: none;
}

.hero-label {
  font-size: 19px;
  font-weight: 600;
  letter-spacing: 3px;
  text-transform: uppercase;
  color: #f0a500;
  margin: 0 0 10px;
}

.hero-title {
  font-size: 52px;
  font-weight: 800;
  margin: 0 0 8px;
  line-height: 1;
  letter-spacing: -1px;
}

.hero-sub {
  font-size: 18px;
  color: #b0b0b0;
  margin: 0 0 24px;
}

.hero-stats {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 28px;
}

.stat {
  display: flex;
  flex-direction: column;
}

.stat-value {
  font-size: 26px;
  font-weight: 700;
  color: #ffffff;
}

.stat-label {
  font-size: 16px;
  color: #b5b5b5;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.divider {
  width: 1px;
  height: 32px;
  background: #ddd;
}

.cta {
  background: #f0a500;
  color: #ffffff;
  border: none;
  padding: 12px 28px;
  font-size: 18px;
  font-weight: 700;
  border-radius: 4px;
  cursor: pointer;
  letter-spacing: 0.5px;
  transition: background 0.2s;
  pointer-events: all;
}

.cta:hover {
  background: #ffc02e;
}

button.left,
button.right {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  font-size: 100px;
  background: transparent;
  color: grey;
  border: none;
  cursor: pointer;
  z-index: 10;
}

.left { left: 20px; }
.right { right: 20px; }
</style>