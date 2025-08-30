import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import AppLayout from '@/layouts/app-layout'
import { dashboard } from '@/routes'
import { type BreadcrumbItem, type SharedData } from '@/types'
import { Head, usePage, router } from '@inertiajs/react'
import { 
  Calendar, 
  Users, 
  TrendingUp,
  Clock,
  DollarSign,
  UserPlus,
  Phone,
  Mail,
  Stethoscope,
  Plus
} from 'lucide-react'

interface Patient {
  id: number
  name: string
  species: string
  breed: string
  owner_name: string
  owner_phone: string
  owner_email?: string
  created_at: string
  age?: string
}

interface User {
  id: number
  name: string
}

interface Appointment {
  id: number
  scheduled_at: string
  duration_minutes: number
  status: string
  type: string
  reason: string
  price?: number
  status_color: string
  patient: Patient
  doctor: User
}

interface Stats {
  total_patients: number
  branch_patients: number
  today_appointments: number
  pending_appointments: number
}

interface WeekStats {
  completed_appointments: number
  week_revenue: number
  new_patients: number
}

interface Props {
  stats: Stats
  weekStats: WeekStats
  todayAppointments: Appointment[]
  upcomingAppointments: Appointment[]
  recentPatients: Patient[]
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

export default function Dashboard({ 
  stats, 
  weekStats, 
  todayAppointments, 
  upcomingAppointments, 
  recentPatients 
}: Props) {
  const formatTime = (datetime: string) => {
    return new Date(datetime).toLocaleTimeString('es', { 
      hour: '2-digit', 
      minute: '2-digit' 
    })
  }

  const formatDate = (datetime: string) => {
    return new Date(datetime).toLocaleDateString('es', { 
      month: 'short', 
      day: 'numeric' 
    })
  }

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'scheduled': return 'bg-blue-100 text-blue-800'
      case 'confirmed': return 'bg-green-100 text-green-800'
      case 'in_progress': return 'bg-yellow-100 text-yellow-800'
      case 'completed': return 'bg-gray-100 text-gray-800'
      case 'cancelled': return 'bg-red-100 text-red-800'
      default: return 'bg-gray-100 text-gray-800'
    }
  }

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Dashboard" />
      
      <div className="p-6 space-y-6">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p className="text-gray-600">Resumen de actividad de tu clínica veterinaria</p>
          </div>
        </div>
            

        {/* Action Buttons */}
        <div className="flex gap-2">
          <Button className="gap-2">
            <Plus className="h-4 w-4" />
            Nueva Cita
          </Button>
          <Button variant="outline" className="gap-2">
            <UserPlus className="h-4 w-4" />
            Nuevo Paciente
          </Button>
        </div>

        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Pacientes Totales</CardTitle>
              <Users className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{stats?.total_patients || 0}</div>
              {stats?.branch_patients > 0 && (
                <p className="text-xs text-muted-foreground">
                  {stats.branch_patients} en esta sucursal
                </p>
              )}
            </CardContent>
          </Card>
          
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Citas Hoy</CardTitle>
              <Calendar className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{stats?.today_appointments || 0}</div>
              <p className="text-xs text-muted-foreground">
                {stats?.pending_appointments || 0} pendientes
              </p>
            </CardContent>
          </Card>
          
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Ingresos Semana</CardTitle>
              <DollarSign className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">${Number(weekStats?.week_revenue || 0).toFixed(2)}</div>
              <p className="text-xs text-muted-foreground">
                {weekStats?.completed_appointments || 0} citas completadas
              </p>
            </CardContent>
          </Card>
          
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Nuevos Pacientes</CardTitle>
              <TrendingUp className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{weekStats?.new_patients || 0}</div>
              <p className="text-xs text-muted-foreground">
                Esta semana
              </p>
            </CardContent>
          </Card>
        </div>

        <div className="grid lg:grid-cols-3 gap-6">
          {/* Today's Appointments */}
          <div className="lg:col-span-2">
            <Card>
              <CardHeader>
                <div className="flex items-center justify-between">
                  <div>
                    <CardTitle>Citas de Hoy</CardTitle>
                    <CardDescription>
                      {todayAppointments?.length || 0} citas programadas
                    </CardDescription>
                  </div>
                  <Button variant="outline" size="sm">
                    Ver todas
                  </Button>
                </div>
              </CardHeader>
              <CardContent>
                {!todayAppointments || todayAppointments.length === 0 ? (
                  <div className="text-center py-8">
                    <Calendar className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                    <p className="text-gray-500">No hay citas programadas para hoy</p>
                  </div>
                ) : (
                  <div className="space-y-4">
                    {todayAppointments.map((appointment) => (
                      <div key={appointment.id} className="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50">
                        <div className="flex items-center gap-4">
                          <div className="flex flex-col items-center">
                            <Clock className="h-4 w-4 text-gray-500" />
                            <span className="text-sm font-medium">
                              {formatTime(appointment.scheduled_at)}
                            </span>
                          </div>
                          <div className="flex-1">
                            <div className="flex items-center gap-2 mb-1">
                              <h4 className="font-medium">{appointment.patient.name}</h4>
                              <Badge className={getStatusColor(appointment.status)}>
                                {appointment.status}
                              </Badge>
                            </div>
                            <p className="text-sm text-gray-600">
                              {appointment.patient.species} • {appointment.patient.owner_name}
                            </p>
                            <p className="text-xs text-gray-500">
                              {appointment.type} • Dr. {appointment.doctor.name}
                            </p>
                          </div>
                        </div>
                        <div className="text-right">
                          {appointment.price && (
                            <p className="text-sm font-medium">${Number(appointment.price).toFixed(2)}</p>
                          )}
                          <p className="text-xs text-gray-500">{appointment.duration_minutes} min</p>
                        </div>
                      </div>
                    ))}
                  </div>
                )}
              </CardContent>
            </Card>
          </div>

          <div className="space-y-6">
            {/* Upcoming Appointments */}
            <Card>
              <CardHeader>
                <CardTitle>Próximas Citas</CardTitle>
                <CardDescription>
                  Siguientes {upcomingAppointments?.length || 0} citas programadas
                </CardDescription>
              </CardHeader>
              <CardContent>
                {!upcomingAppointments || upcomingAppointments.length === 0 ? (
                  <p className="text-gray-500 text-center py-4">No hay citas próximas</p>
                ) : (
                  <div className="space-y-3">
                    {upcomingAppointments.map((appointment) => (
                      <div key={appointment.id} className="flex items-center gap-3 p-2 border rounded">
                        <div className="text-center">
                          <p className="text-xs text-gray-500">
                            {formatDate(appointment.scheduled_at)}
                          </p>
                          <p className="text-sm font-medium">
                            {formatTime(appointment.scheduled_at)}
                          </p>
                        </div>
                        <div className="flex-1 min-w-0">
                          <p className="font-medium truncate">{appointment.patient.name}</p>
                          <p className="text-xs text-gray-500 truncate">
                            {appointment.type}
                          </p>
                        </div>
                      </div>
                    ))}
                  </div>
                )}
              </CardContent>
            </Card>

            {/* Recent Patients */}
            <Card>
              <CardHeader>
                <CardTitle>Pacientes Recientes</CardTitle>
                <CardDescription>
                  Últimos pacientes registrados
                </CardDescription>
              </CardHeader>
              <CardContent>
                {!recentPatients || recentPatients.length === 0 ? (
                  <p className="text-gray-500 text-center py-4">No hay pacientes recientes</p>
                ) : (
                  <div className="space-y-3">
                    {recentPatients.map((patient) => (
                      <div key={patient.id} className="flex items-center gap-3 p-2 border rounded">
                        <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                          <Stethoscope className="h-4 w-4 text-blue-600" />
                        </div>
                        <div className="flex-1 min-w-0">
                          <p className="font-medium truncate">{patient.name}</p>
                          <p className="text-xs text-gray-500">
                            {patient.species} • {patient.breed}
                          </p>
                          <p className="text-xs text-gray-500 truncate">
                            {patient.owner_name}
                          </p>
                        </div>
                        <div className="flex flex-col gap-1">
                          {patient.owner_phone && (
                            <a 
                              href={`tel:${patient.owner_phone}`}
                              className="text-blue-600 hover:text-blue-800"
                            >
                              <Phone className="h-3 w-3" />
                            </a>
                          )}
                          {patient.owner_email && (
                            <a 
                              href={`mailto:${patient.owner_email}`}
                              className="text-blue-600 hover:text-blue-800"
                            >
                              <Mail className="h-3 w-3" />
                            </a>
                          )}
                        </div>
                      </div>
                    ))}
                  </div>
                )}
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </AppLayout>
  )
}
