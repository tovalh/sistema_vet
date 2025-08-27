import { useEffect } from 'react'

export default function ClearTheme() {
  useEffect(() => {
    // Limpiar localStorage
    localStorage.removeItem('appearance')
    
    // Forzar modo claro
    document.documentElement.classList.remove('dark')
    document.documentElement.style.colorScheme = 'light'
    
    // Establecer cookie de apariencia en light
    document.cookie = 'appearance=light;path=/;max-age=31536000;SameSite=Lax'
    
    // Redirigir a home después de limpiar
    window.location.href = '/'
  }, [])

  return <div>Limpiando configuración de tema...</div>
}