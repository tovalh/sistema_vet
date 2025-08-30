import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import AdminLayout from '@/layouts/admin-layout'
import { type BreadcrumbItem } from '@/types'
import { Head, Link } from '@inertiajs/react'
import { 
  Building2, 
  Users, 
  CreditCard, 
  TrendingUp,
  Calendar,
  Mail,
  Phone
} from 'lucide-react'

interface Tenant {
  id: number
  name: string
  slug: string
  email: string
  phone: string
  address: string
  status: string
  trial_ends_at: string
  users_count: number
  branches_count: number
  patients_count: number
  active_subscription: {
    plan: {
      name: string
      price: number
    }
  } | null
}

interface Plan {
  id: number
  name: string
  price: number
  subscriptions_count: number
}

interface Stats {
  total_tenants: number
  active_tenants: number
  total_users: number
  total_subscriptions: number
  total_branches: number
  total_patients: number
  total_appointments: number
  monthly_revenue: number
}

interface Props {
  stats: Stats
  tenants: Tenant[]
  plans: Plan[]
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: '/admin',
    },
];

export default function AdminDashboard({ stats, tenants, plans }: Props) {
  const getStatusColor = (status: string) => {
    switch (status) {
      case 'active': return 'bg-green-100 text-green-800'
      case 'suspended': return 'bg-red-100 text-red-800'
      case 'pending': return 'bg-yellow-100 text-yellow-800'
      default: return 'bg-gray-100 text-gray-800'
    }
  }

  return (
    <AdminLayout breadcrumbs={breadcrumbs}>
      <Head title="Panel de Administración - Sistema Veterinario" />
      
      <div className="p-6">
        <div className="mb-8 flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold text-gray-900">Panel de Super Administración</h1>
            <p className="text-gray-600">Vista global del sistema multi-tenancy</p>
          </div>
          <div className="flex gap-2">
            <Button asChild>
              <Link href="/admin/clinics">Gestionar Clínicas</Link>
            </Button>
            <Button variant="outline" asChild>
              <Link href="/admin/users">Gestionar Usuarios</Link>
            </Button>
          </div>
        </div>

        {/* Stats Cards */}
        <div className="grid md:grid-cols-4 gap-6 mb-8">
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Total Clínicas</CardTitle>
              <Building2 className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{stats.total_tenants}</div>
              <p className="text-xs text-muted-foreground">
                {stats.active_tenants} activas, {stats.total_branches} sucursales
              </p>
            </CardContent>
          </Card>
          
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Usuarios y Pacientes</CardTitle>
              <Users className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{stats.total_users}</div>
              <p className="text-xs text-muted-foreground">
                {stats.total_patients} pacientes registrados
              </p>
            </CardContent>
          </Card>
          
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Actividad Total</CardTitle>
              <CreditCard className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{stats.total_appointments}</div>
              <p className="text-xs text-muted-foreground">
                Citas programadas en total
              </p>
            </CardContent>
          </Card>
          
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Ingresos Mensuales</CardTitle>
              <TrendingUp className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">
                ${plans.reduce((acc, plan) => acc + (plan.price * plan.subscriptions_count), 0).toFixed(2)}
              </div>
              <p className="text-xs text-muted-foreground">
                {stats.total_subscriptions} suscripciones activas
              </p>
            </CardContent>
          </Card>
        </div>

        <div className="grid lg:grid-cols-3 gap-6">
          {/* Tenants List */}
          <div className="lg:col-span-2">
            <Card>
              <CardHeader>
                <CardTitle>Clínicas Registradas</CardTitle>
                <CardDescription>
                  Lista de todas las clínicas veterinarias en el sistema
                </CardDescription>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {tenants.map((tenant) => (
                    <div key={tenant.id} className="border rounded-lg p-4 space-y-3">
                      <div className="flex items-center justify-between">
                        <div>
                          <h3 className="font-semibold text-lg">{tenant.name}</h3>
                          <p className="text-sm text-gray-600">/{tenant.slug}</p>
                        </div>
                        <Badge className={getStatusColor(tenant.status)}>
                          {tenant.status}
                        </Badge>
                      </div>
                      
                      <div className="grid md:grid-cols-2 gap-4 text-sm">
                        <div className="flex items-center gap-2">
                          <Mail className="h-4 w-4 text-gray-500" />
                          <span>{tenant.email}</span>
                        </div>
                        {tenant.phone && (
                          <div className="flex items-center gap-2">
                            <Phone className="h-4 w-4 text-gray-500" />
                            <span>{tenant.phone}</span>
                          </div>
                        )}
                        <div className="flex items-center gap-2">
                          <Users className="h-4 w-4 text-gray-500" />
                          <span>{tenant.users_count} usuarios en {tenant.branches_count} sucursales</span>
                        </div>
                        {tenant.trial_ends_at && (
                          <div className="flex items-center gap-2">
                            <Calendar className="h-4 w-4 text-gray-500" />
                            <span>Prueba hasta: {new Date(tenant.trial_ends_at).toLocaleDateString()}</span>
                          </div>
                        )}
                      </div>
                      
                      {tenant.active_subscription && (
                        <div className="bg-blue-50 p-2 rounded">
                          <span className="text-sm font-medium text-blue-800">
                            Plan: {tenant.active_subscription.plan.name} 
                            (${tenant.active_subscription.plan.price}/mes)
                          </span>
                        </div>
                      )}
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          </div>

          {/* Plans Overview */}
          <div>
            <Card>
              <CardHeader>
                <CardTitle>Planes de Suscripción</CardTitle>
                <CardDescription>
                  Estadísticas por plan
                </CardDescription>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {plans.map((plan) => (
                    <div key={plan.id} className="border rounded-lg p-4">
                      <div className="flex justify-between items-start mb-2">
                        <h3 className="font-semibold">{plan.name}</h3>
                        <Badge variant="secondary">
                          {plan.subscriptions_count} activas
                        </Badge>
                      </div>
                      <p className="text-2xl font-bold text-blue-600 mb-2">
                        ${plan.price}/mes
                      </p>
                      <p className="text-sm text-gray-600">
                        Ingreso mensual: ${(plan.price * plan.subscriptions_count).toFixed(2)}
                      </p>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </AdminLayout>
  )
}