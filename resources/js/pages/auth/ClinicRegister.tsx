import { useState } from 'react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Checkbox } from '@/components/ui/checkbox'
import { Badge } from '@/components/ui/badge'
import { Head, Link, useForm } from '@inertiajs/react'
import { Stethoscope, Building, User, CreditCard, Check, ArrowLeft } from 'lucide-react'
import InputError from '@/components/input-error'

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

export default function ClinicRegister({ plans }: Props) {
  const [currentStep, setCurrentStep] = useState(1)
  const [selectedPlan, setSelectedPlan] = useState<SubscriptionPlan | null>(null)

  const { data, setData, post, processing, errors } = useForm({
    clinic_name: '',
    clinic_email: '',
    clinic_phone: '',
    clinic_address: '',
    owner_name: '',
    owner_email: '',
    password: '',
    password_confirmation: '',
    plan_id: '',
    terms: false,
  })

  const nextStep = () => {
    if (currentStep < 3) setCurrentStep(currentStep + 1)
  }

  const prevStep = () => {
    if (currentStep > 1) setCurrentStep(currentStep - 1)
  }

  const selectPlan = (plan: SubscriptionPlan) => {
    setSelectedPlan(plan)
    setData('plan_id', plan.id.toString())
    nextStep()
  }

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    post('/clinic/register')
  }

  const steps = [
    { number: 1, title: 'Información de la Clínica', icon: <Building className="h-5 w-5" /> },
    { number: 2, title: 'Seleccionar Plan', icon: <CreditCard className="h-5 w-5" /> },
    { number: 3, title: 'Cuenta del Administrador', icon: <User className="h-5 w-5" /> },
  ]

  return (
    <>
      <Head title="Registrar Clínica - Sistema Veterinario" />
      
      <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50">
        {/* Header */}
        <header className="border-b bg-white/80 backdrop-blur-sm">
          <div className="container mx-auto px-4 py-4">
            <div className="flex items-center justify-between">
              <Link href="/" className="flex items-center space-x-2">
                <Stethoscope className="h-8 w-8 text-blue-600" />
                <span className="text-xl font-bold text-gray-900">SistemaVet</span>
              </Link>
              <Button variant="ghost" asChild>
                <Link href="/" className="flex items-center">
                  <ArrowLeft className="mr-2 h-4 w-4" />
                  Volver al inicio
                </Link>
              </Button>
            </div>
          </div>
        </header>

        <div className="container mx-auto px-4 py-12">
          <div className="max-w-4xl mx-auto">
            {/* Progress Steps */}
            <div className="mb-12">
              <div className="flex items-center justify-center">
                {steps.map((step, index) => (
                  <div key={step.number} className="flex items-center">
                    <div className={`flex items-center justify-center w-12 h-12 rounded-full border-2 ${
                      currentStep >= step.number 
                        ? 'bg-blue-600 border-blue-600 text-white' 
                        : 'bg-white border-gray-300 text-gray-400'
                    }`}>
                      {currentStep > step.number ? <Check className="h-6 w-6" /> : step.icon}
                    </div>
                    <div className="ml-4 text-sm font-medium">
                      <div className={currentStep >= step.number ? 'text-blue-600' : 'text-gray-500'}>
                        Paso {step.number}
                      </div>
                      <div className={currentStep >= step.number ? 'text-gray-900' : 'text-gray-500'}>
                        {step.title}
                      </div>
                    </div>
                    {index < steps.length - 1 && (
                      <div className={`mx-8 w-16 h-1 ${
                        currentStep > step.number ? 'bg-blue-600' : 'bg-gray-300'
                      }`} />
                    )}\n                  </div>
                ))}
              </div>
            </div>

            <form onSubmit={handleSubmit}>
              {/* Step 1: Clinic Information */}
              {currentStep === 1 && (
                <Card className="shadow-xl">
                  <CardHeader className="text-center">
                    <CardTitle className="text-2xl">Información de tu Clínica</CardTitle>
                    <CardDescription>
                      Cuéntanos sobre tu clínica veterinaria
                    </CardDescription>
                  </CardHeader>
                  <CardContent className="space-y-6">
                    <div className="grid md:grid-cols-2 gap-6">
                      <div className="space-y-2">
                        <Label htmlFor="clinic_name">Nombre de la Clínica *</Label>
                        <Input
                          id="clinic_name"
                          type="text"
                          value={data.clinic_name}
                          onChange={e => setData('clinic_name', e.target.value)}
                          placeholder="Ej: Clínica Veterinaria San José"
                        />
                        <InputError message={errors.clinic_name} />
                      </div>
                      <div className="space-y-2">
                        <Label htmlFor="clinic_email">Email de la Clínica *</Label>
                        <Input
                          id="clinic_email"
                          type="email"
                          value={data.clinic_email}
                          onChange={e => setData('clinic_email', e.target.value)}
                          placeholder="contacto@clinicavet.com"
                        />
                        <InputError message={errors.clinic_email} />
                      </div>
                    </div>
                    <div className="grid md:grid-cols-2 gap-6">
                      <div className="space-y-2">
                        <Label htmlFor="clinic_phone">Teléfono</Label>
                        <Input
                          id="clinic_phone"
                          type="tel"
                          value={data.clinic_phone}
                          onChange={e => setData('clinic_phone', e.target.value)}
                          placeholder="+1 234 567 8900"
                        />
                        <InputError message={errors.clinic_phone} />
                      </div>
                      <div className="space-y-2">
                        <Label htmlFor="clinic_address">Dirección</Label>
                        <Input
                          id="clinic_address"
                          type="text"
                          value={data.clinic_address}
                          onChange={e => setData('clinic_address', e.target.value)}
                          placeholder="Calle Principal #123"
                        />
                        <InputError message={errors.clinic_address} />
                      </div>
                    </div>
                    <div className="flex justify-end">
                      <Button 
                        type="button" 
                        onClick={nextStep}
                        disabled={!data.clinic_name || !data.clinic_email}
                      >
                        Continuar
                      </Button>
                    </div>
                  </CardContent>
                </Card>
              )}

              {/* Step 2: Plan Selection */}
              {currentStep === 2 && (
                <div>
                  <div className="text-center mb-8">
                    <h2 className="text-2xl font-bold text-gray-900 mb-2">Selecciona tu Plan</h2>
                    <p className="text-gray-600">
                      Todos los planes incluyen 14 días de prueba gratuita
                    </p>
                  </div>
                  <div className="grid md:grid-cols-3 gap-6 mb-6">
                    {plans.map((plan) => (
                      <Card 
                        key={plan.id} 
                        className={`cursor-pointer transition-all ${
                          selectedPlan?.id === plan.id 
                            ? 'ring-2 ring-blue-500 shadow-lg' 
                            : 'hover:shadow-lg hover:scale-105'
                        }`}
                        onClick={() => selectPlan(plan)}
                      >
                        {plan.slug === 'profesional' && (
                          <Badge className="absolute -top-3 left-1/2 transform -translate-x-1/2">
                            Recomendado
                          </Badge>
                        )}
                        <CardHeader className="text-center">
                          <CardTitle>{plan.name}</CardTitle>
                          <CardDescription>{plan.description}</CardDescription>
                          <div className="mt-4">
                            <span className="text-3xl font-bold">${plan.price}</span>
                            <span className="text-gray-600">/mes</span>
                          </div>
                          <p className="text-sm text-green-600 font-medium">
                            ¡Gratis por 14 días!
                          </p>
                        </CardHeader>
                        <CardContent>
                          <ul className="space-y-2">
                            {plan.features.slice(0, 4).map((feature, index) => (
                              <li key={index} className="flex items-center gap-2">
                                <Check className="h-4 w-4 text-green-500" />
                                <span className="text-sm">{feature}</span>
                              </li>
                            ))}
                          </ul>
                        </CardContent>
                      </Card>
                    ))}
                  </div>
                  <div className="flex justify-between">
                    <Button type="button" variant="outline" onClick={prevStep}>
                      Atrás
                    </Button>
                    <Button 
                      type="button" 
                      onClick={nextStep}
                      disabled={!selectedPlan}
                    >
                      Continuar
                    </Button>
                  </div>
                </div>
              )}

              {/* Step 3: Owner Account */}
              {currentStep === 3 && (
                <Card className="shadow-xl">
                  <CardHeader className="text-center">
                    <CardTitle className="text-2xl">Cuenta del Administrador</CardTitle>
                    <CardDescription>
                      Crea la cuenta para el administrador de la clínica
                    </CardDescription>
                  </CardHeader>
                  <CardContent className="space-y-6">
                    {selectedPlan && (
                      <div className="bg-blue-50 p-4 rounded-lg">
                        <div className="flex items-center justify-between">
                          <div>
                            <h3 className="font-medium">Plan seleccionado: {selectedPlan.name}</h3>
                            <p className="text-sm text-gray-600">14 días gratis, luego ${selectedPlan.price}/mes</p>
                          </div>
                          <Button 
                            type="button" 
                            variant="ghost" 
                            size="sm" 
                            onClick={() => setCurrentStep(2)}
                          >
                            Cambiar
                          </Button>
                        </div>
                      </div>
                    )}
                    
                    <div className="grid md:grid-cols-2 gap-6">
                      <div className="space-y-2">
                        <Label htmlFor="owner_name">Nombre Completo *</Label>
                        <Input
                          id="owner_name"
                          type="text"
                          value={data.owner_name}
                          onChange={e => setData('owner_name', e.target.value)}
                          placeholder="Dr. Juan Pérez"
                        />
                        <InputError message={errors.owner_name} />
                      </div>
                      <div className="space-y-2">
                        <Label htmlFor="owner_email">Email *</Label>
                        <Input
                          id="owner_email"
                          type="email"
                          value={data.owner_email}
                          onChange={e => setData('owner_email', e.target.value)}
                          placeholder="juan.perez@email.com"
                        />
                        <InputError message={errors.owner_email} />
                      </div>
                    </div>
                    
                    <div className="grid md:grid-cols-2 gap-6">
                      <div className="space-y-2">
                        <Label htmlFor="password">Contraseña *</Label>
                        <Input
                          id="password"
                          type="password"
                          value={data.password}
                          onChange={e => setData('password', e.target.value)}
                        />
                        <InputError message={errors.password} />
                      </div>
                      <div className="space-y-2">
                        <Label htmlFor="password_confirmation">Confirmar Contraseña *</Label>
                        <Input
                          id="password_confirmation"
                          type="password"
                          value={data.password_confirmation}
                          onChange={e => setData('password_confirmation', e.target.value)}
                        />
                        <InputError message={errors.password_confirmation} />
                      </div>
                    </div>

                    <div className="flex items-start space-x-2">
                      <Checkbox
                        id="terms"
                        checked={data.terms}
                        onCheckedChange={(checked) => setData('terms', !!checked)}
                      />
                      <Label htmlFor="terms" className="text-sm leading-5">
                        Acepto los{' '}
                        <Link href="/terms" className="text-blue-600 hover:underline">
                          términos y condiciones
                        </Link>
                        {' '}y la{' '}
                        <Link href="/privacy" className="text-blue-600 hover:underline">
                          política de privacidad
                        </Link>
                      </Label>
                    </div>
                    <InputError message={errors.terms} />

                    <div className="flex justify-between">
                      <Button type="button" variant="outline" onClick={prevStep}>
                        Atrás
                      </Button>
                      <Button 
                        type="submit" 
                        disabled={processing || !data.terms}
                        className="bg-green-600 hover:bg-green-700"
                      >
                        {processing ? 'Registrando...' : 'Crear Clínica'}
                      </Button>
                    </div>
                  </CardContent>
                </Card>
              )}
            </form>

            <div className="text-center mt-8">
              <p className="text-gray-600">
                ¿Ya tienes una cuenta?{' '}
                <Link href="/login" className="text-blue-600 hover:underline">
                  Inicia sesión aquí
                </Link>
              </p>
            </div>
          </div>
        </div>
      </div>
    </>
  )
}