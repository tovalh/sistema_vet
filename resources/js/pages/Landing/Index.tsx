import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Head, Link } from '@inertiajs/react'
import { 
  Stethoscope, 
  Calendar, 
  Users, 
  FileText, 
  TrendingUp,
  Shield,
  Clock,
  Heart,
  Check,
  ArrowRight
} from 'lucide-react'

interface SubscriptionPlan {
  id: number
  name: string
  slug: string
  description: string
  price: number
  features: string[]
  max_users: number
  max_patients: number | null
  has_inventory: boolean
  has_reports: boolean
  has_api_access: boolean
}

interface Props {
  plans: SubscriptionPlan[]
}

export default function LandingIndex({ plans }: Props) {
  const features = [
    {
      icon: <Stethoscope className="h-6 w-6" />,
      title: "Gestión de Pacientes",
      description: "Administra la información completa de tus pacientes con historias clínicas digitales."
    },
    {
      icon: <Calendar className="h-6 w-6" />,
      title: "Sistema de Citas",
      description: "Programa y gestiona citas de manera eficiente con recordatorios automáticos."
    },
    {
      icon: <Users className="h-6 w-6" />,
      title: "Gestión de Personal",
      description: "Controla el acceso y permisos de tu equipo veterinario."
    },
    {
      icon: <FileText className="h-6 w-6" />,
      title: "Facturación",
      description: "Genera facturas y lleva el control financiero de tu clínica."
    },
    {
      icon: <TrendingUp className="h-6 w-6" />,
      title: "Reportes",
      description: "Analiza el rendimiento de tu clínica con reportes detallados."
    },
    {
      icon: <Shield className="h-6 w-6" />,
      title: "Seguridad",
      description: "Protege la información de tus pacientes con los más altos estándares."
    }
  ]

  const benefits = [
    {
      icon: <Clock className="h-5 w-5" />,
      text: "Ahorra tiempo en tareas administrativas"
    },
    {
      icon: <Heart className="h-5 w-5" />,
      text: "Mejora la atención a tus pacientes"
    },
    {
      icon: <TrendingUp className="h-5 w-5" />,
      text: "Aumenta la eficiencia de tu clínica"
    }
  ]

  return (
    <>
      <Head title="Sistema Veterinario - Gestiona tu clínica con facilidad" />
      
      <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50">
        {/* Header */}
        <header className="border-b bg-white/80 backdrop-blur-sm sticky top-0 z-50">
          <div className="container mx-auto px-4 py-4">
            <div className="flex items-center justify-between">
              <div className="flex items-center space-x-2">
                <Stethoscope className="h-8 w-8 text-blue-600" />
                <span className="text-xl font-bold text-gray-900">SistemaVet</span>
              </div>
              <nav className="hidden md:flex items-center space-x-8">
                <a href="#features" className="text-gray-600 hover:text-blue-600 transition-colors">Características</a>
                <a href="#pricing" className="text-gray-600 hover:text-blue-600 transition-colors">Precios</a>
                <Link href="/login" className="text-gray-600 hover:text-blue-600 transition-colors">Iniciar Sesión</Link>
                <Button asChild>
                  <Link href="/register">Prueba Gratis</Link>
                </Button>
              </nav>
            </div>
          </div>
        </header>

        {/* Hero Section */}
        <section className="py-20">
          <div className="container mx-auto px-4 text-center">
            <Badge variant="secondary" className="mb-6">
              🎉 Prueba gratuita por 14 días
            </Badge>
            <h1 className="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
              La solución completa para
              <span className="text-blue-600 block">tu clínica veterinaria</span>
            </h1>
            <p className="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
              Gestiona pacientes, citas, inventario y facturación desde una sola plataforma. 
              Diseñado especialmente para veterinarios modernos.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Button size="lg" asChild>
                <Link href="/register" className="flex items-center">
                  Comenzar Ahora
                  <ArrowRight className="ml-2 h-5 w-5" />
                </Link>
              </Button>
              <Button variant="outline" size="lg" asChild>
                <Link href="#demo">Ver Demo</Link>
              </Button>
            </div>
            <div className="flex items-center justify-center gap-8 mt-12">
              {benefits.map((benefit, index) => (
                <div key={index} className="flex items-center gap-2 text-gray-600">
                  {benefit.icon}
                  <span className="text-sm">{benefit.text}</span>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* Features Section */}
        <section id="features" className="py-20 bg-white">
          <div className="container mx-auto px-4">
            <div className="text-center mb-16">
              <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Todo lo que necesitas en un solo lugar
              </h2>
              <p className="text-xl text-gray-600 max-w-2xl mx-auto">
                Funcionalidades diseñadas específicamente para clínicas veterinarias
              </p>
            </div>
            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
              {features.map((feature, index) => (
                <Card key={index} className="border-0 shadow-lg hover:shadow-xl transition-shadow">
                  <CardHeader>
                    <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 mb-4">
                      {feature.icon}
                    </div>
                    <CardTitle>{feature.title}</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <CardDescription className="text-gray-600">
                      {feature.description}
                    </CardDescription>
                  </CardContent>
                </Card>
              ))}
            </div>
          </div>
        </section>

        {/* Pricing Preview */}
        <section id="pricing" className="py-20 bg-gray-50">
          <div className="container mx-auto px-4">
            <div className="text-center mb-16">
              <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Planes para cada tipo de clínica
              </h2>
              <p className="text-xl text-gray-600 max-w-2xl mx-auto">
                Escoge el plan que mejor se adapte a las necesidades de tu clínica
              </p>
            </div>
            <div className="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
              {plans.map((plan) => (
                <Card key={plan.id} className={`relative ${plan.slug === 'profesional' ? 'ring-2 ring-blue-500 scale-105' : ''}`}>
                  {plan.slug === 'profesional' && (
                    <Badge className="absolute -top-3 left-1/2 transform -translate-x-1/2">
                      Más Popular
                    </Badge>
                  )}
                  <CardHeader>
                    <CardTitle>{plan.name}</CardTitle>
                    <CardDescription>{plan.description}</CardDescription>
                    <div className="mt-4">
                      <span className="text-3xl font-bold">${plan.price}</span>
                      <span className="text-gray-600">/mes</span>
                    </div>
                  </CardHeader>
                  <CardContent>
                    <ul className="space-y-2 mb-6">
                      {plan.features.slice(0, 4).map((feature, index) => (
                        <li key={index} className="flex items-center gap-2">
                          <Check className="h-4 w-4 text-green-500" />
                          <span className="text-sm">{feature}</span>
                        </li>
                      ))}
                    </ul>
                    <Button className="w-full" variant={plan.slug === 'profesional' ? 'default' : 'outline'}>
                      Comenzar Prueba
                    </Button>
                  </CardContent>
                </Card>
              ))}
            </div>
            <div className="text-center mt-8">
              <Button variant="link" asChild>
                <Link href="/pricing">Ver todos los planes y características</Link>
              </Button>
            </div>
          </div>
        </section>

        {/* CTA Section */}
        <section className="py-20 bg-blue-600">
          <div className="container mx-auto px-4 text-center">
            <h2 className="text-3xl md:text-4xl font-bold text-white mb-4">
              ¿Listo para transformar tu clínica?
            </h2>
            <p className="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
              Únete a cientos de veterinarios que ya confían en SistemaVet
            </p>
            <Button size="lg" variant="secondary" asChild>
              <Link href="/register">Prueba Gratis por 14 Días</Link>
            </Button>
          </div>
        </section>

        {/* Footer */}
        <footer className="py-12 bg-gray-900 text-white">
          <div className="container mx-auto px-4">
            <div className="flex flex-col md:flex-row justify-between items-center">
              <div className="flex items-center space-x-2 mb-4 md:mb-0">
                <Stethoscope className="h-6 w-6" />
                <span className="text-lg font-bold">SistemaVet</span>
              </div>
              <div className="flex space-x-6">
                <Link href="/privacy" className="text-gray-300 hover:text-white">Privacidad</Link>
                <Link href="/terms" className="text-gray-300 hover:text-white">Términos</Link>
                <Link href="/support" className="text-gray-300 hover:text-white">Soporte</Link>
              </div>
            </div>
            <div className="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
              <p>&copy; 2024 SistemaVet. Todos los derechos reservados.</p>
            </div>
          </div>
        </footer>
      </div>
    </>
  )
}