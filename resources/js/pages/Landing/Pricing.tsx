import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Head, Link } from '@inertiajs/react'
import { Check, X, Stethoscope, ArrowLeft } from 'lucide-react'

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

export default function LandingPricing({ plans }: Props) {
  const allFeatures = [
    'Gestión de pacientes',
    'Sistema de citas',
    'Historias clínicas',
    'Facturación básica',
    'Soporte por email',
    'Inventario',
    'Reportes avanzados',
    'Soporte prioritario',
    'Acceso API',
    'Integraciones',
    'Soporte 24/7',
    'Backups automáticos'
  ]

  const getFeatureStatus = (plan: SubscriptionPlan, feature: string) => {
    const featureMap: Record<string, keyof SubscriptionPlan | boolean> = {
      'Gestión de pacientes': true,
      'Sistema de citas': true,
      'Historias clínicas': true,
      'Facturación básica': true,
      'Soporte por email': true,
      'Inventario': plan.has_inventory,
      'Reportes avanzados': plan.has_reports,
      'Soporte prioritario': plan.slug !== 'basico',
      'Acceso API': plan.has_api_access,
      'Integraciones': plan.has_api_access,
      'Soporte 24/7': plan.slug === 'premium',
      'Backups automáticos': plan.slug === 'premium'
    }

    return featureMap[feature] || false
  }

  return (
    <>
      <Head title="Precios - Sistema Veterinario" />
      
      <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50">
        {/* Header */}
        <header className="border-b bg-white/80 backdrop-blur-sm sticky top-0 z-50">
          <div className="container mx-auto px-4 py-4">
            <div className="flex items-center justify-between">
              <div className="flex items-center space-x-2">
                <Link href="/" className="flex items-center space-x-2">
                  <Stethoscope className="h-8 w-8 text-blue-600" />
                  <span className="text-xl font-bold text-gray-900">SistemaVet</span>
                </Link>
              </div>
              <nav className="flex items-center space-x-8">
                <Button variant="ghost" asChild>
                  <Link href="/" className="flex items-center">
                    <ArrowLeft className="mr-2 h-4 w-4" />
                    Volver
                  </Link>
                </Button>
                <Link href="/login" className="text-gray-600 hover:text-blue-600 transition-colors">Iniciar Sesión</Link>
                <Button asChild>
                  <Link href="/register">Prueba Gratis</Link>
                </Button>
              </nav>
            </div>
          </div>
        </header>

        {/* Pricing Section */}
        <section className="py-20">
          <div className="container mx-auto px-4">
            <div className="text-center mb-16">
              <h1 className="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Precios simples y transparentes
              </h1>
              <p className="text-xl text-gray-600 max-w-3xl mx-auto">
                Elige el plan perfecto para tu clínica. Todos incluyen prueba gratuita de 14 días.
                Sin compromisos, cancela cuando quieras.
              </p>
            </div>

            {/* Plans Cards */}
            <div className="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto mb-16">
              {plans.map((plan) => (
                <Card 
                  key={plan.id} 
                  className={`relative ${
                    plan.slug === 'profesional' 
                      ? 'ring-2 ring-blue-500 scale-105 shadow-2xl' 
                      : 'shadow-lg hover:shadow-xl transition-shadow'
                  }`}
                >
                  {plan.slug === 'profesional' && (
                    <Badge className="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-blue-600">
                      Más Popular
                    </Badge>
                  )}
                  <CardHeader className="text-center pb-6">
                    <CardTitle className="text-2xl">{plan.name}</CardTitle>
                    <CardDescription className="text-base">{plan.description}</CardDescription>
                    <div className="mt-6">
                      <span className="text-4xl font-bold text-gray-900">${plan.price}</span>
                      <span className="text-gray-600 ml-1">/mes</span>
                    </div>
                    <p className="text-sm text-gray-500 mt-2">Facturación mensual</p>
                  </CardHeader>
                  <CardContent className="pt-0">
                    <ul className="space-y-4 mb-8">
                      <li className="flex items-center gap-3">
                        <Check className="h-5 w-5 text-green-500 flex-shrink-0" />
                        <span>
                          {plan.max_users === 999999 ? 'Usuarios ilimitados' : `Hasta ${plan.max_users} usuarios`}
                        </span>
                      </li>
                      <li className="flex items-center gap-3">
                        <Check className="h-5 w-5 text-green-500 flex-shrink-0" />
                        <span>
                          {plan.max_patients ? `Hasta ${plan.max_patients} pacientes` : 'Pacientes ilimitados'}
                        </span>
                      </li>
                      {plan.features.map((feature, index) => (
                        <li key={index} className="flex items-center gap-3">
                          <Check className="h-5 w-5 text-green-500 flex-shrink-0" />
                          <span className="text-sm">{feature}</span>
                        </li>
                      ))}
                    </ul>
                    <Button 
                      className="w-full" 
                      variant={plan.slug === 'profesional' ? 'default' : 'outline'}
                      size="lg"
                      asChild
                    >
                      <Link href="/register">
                        Comenzar Prueba Gratuita
                      </Link>
                    </Button>
                  </CardContent>
                </Card>
              ))}
            </div>

            {/* Feature Comparison Table */}
            <div className="bg-white rounded-lg shadow-lg overflow-hidden">
              <div className="px-6 py-4 bg-gray-50 border-b">
                <h3 className="text-xl font-bold text-gray-900">Comparación detallada de características</h3>
              </div>
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead>
                    <tr className="border-b">
                      <th className="text-left py-4 px-6 font-medium text-gray-900">Características</th>
                      {plans.map((plan) => (
                        <th key={plan.id} className="text-center py-4 px-6 font-medium text-gray-900">
                          {plan.name}
                        </th>
                      ))}
                    </tr>
                  </thead>
                  <tbody>
                    {allFeatures.map((feature, index) => (
                      <tr key={index} className={index % 2 === 0 ? 'bg-gray-50' : 'bg-white'}>
                        <td className="py-4 px-6 font-medium text-gray-900">{feature}</td>
                        {plans.map((plan) => (
                          <td key={plan.id} className="text-center py-4 px-6">
                            {getFeatureStatus(plan, feature) ? (
                              <Check className="h-5 w-5 text-green-500 mx-auto" />
                            ) : (
                              <X className="h-5 w-5 text-gray-400 mx-auto" />
                            )}
                          </td>
                        ))}
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </section>

        {/* FAQ Section */}
        <section className="py-20 bg-gray-50">
          <div className="container mx-auto px-4">
            <div className="text-center mb-16">
              <h2 className="text-3xl font-bold text-gray-900 mb-4">
                Preguntas frecuentes
              </h2>
            </div>
            <div className="max-w-3xl mx-auto space-y-8">
              <div className="bg-white rounded-lg p-6 shadow">
                <h3 className="font-bold text-gray-900 mb-2">¿Puedo cambiar de plan en cualquier momento?</h3>
                <p className="text-gray-600">
                  Sí, puedes actualizar o degradar tu plan en cualquier momento. Los cambios se aplicarán en tu próximo ciclo de facturación.
                </p>
              </div>
              <div className="bg-white rounded-lg p-6 shadow">
                <h3 className="font-bold text-gray-900 mb-2">¿Qué incluye la prueba gratuita?</h3>
                <p className="text-gray-600">
                  La prueba gratuita de 14 días incluye acceso completo a todas las funcionalidades del Plan Profesional, sin restricciones.
                </p>
              </div>
              <div className="bg-white rounded-lg p-6 shadow">
                <h3 className="font-bold text-gray-900 mb-2">¿Cómo funciona la facturación?</h3>
                <p className="text-gray-600">
                  La facturación es mensual y se renueva automáticamente. Puedes cancelar en cualquier momento sin penalización.
                </p>
              </div>
            </div>
          </div>
        </section>

        {/* CTA Section */}
        <section className="py-20 bg-blue-600">
          <div className="container mx-auto px-4 text-center">
            <h2 className="text-3xl md:text-4xl font-bold text-white mb-4">
              ¿Listo para comenzar?
            </h2>
            <p className="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
              Prueba SistemaVet gratis por 14 días. No necesitas tarjeta de crédito.
            </p>
            <Button size="lg" variant="secondary" asChild>
              <Link href="/register">Comenzar Prueba Gratuita</Link>
            </Button>
          </div>
        </section>
      </div>
    </>
  )
}