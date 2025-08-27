import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { type Branch } from '@/types'
import { router } from '@inertiajs/react'
import { Building2, Check, ChevronDown } from 'lucide-react'

interface Props {
  currentBranch?: Branch
  availableBranches?: Branch[]
}

export function BranchSelector({ currentBranch, availableBranches = [] }: Props) {
  const handleBranchChange = (branchId: number) => {
    router.post('/branch/switch', { branch_id: branchId }, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        // Recargar la página para aplicar el nuevo contexto de sucursal
        window.location.reload()
      }
    })
  }

  // Show selector only if we have multiple branches
  if (!currentBranch || !availableBranches || availableBranches.length <= 1) {
    return null
  }

  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <Button variant="outline" className="gap-2">
          <Building2 className="h-4 w-4" />
          <span className="hidden sm:inline">
            {currentBranch.name}
          </span>
          <span className="sm:hidden">
            {currentBranch.code}
          </span>
          <ChevronDown className="h-4 w-4" />
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end" className="w-64">
        <DropdownMenuLabel>Cambiar Sucursal</DropdownMenuLabel>
        <DropdownMenuSeparator />
        {availableBranches.map((branch) => (
          <DropdownMenuItem
            key={branch.id}
            onClick={() => handleBranchChange(branch.id)}
            className="flex items-center gap-3"
          >
            <div className="flex-1">
              <div className="flex items-center gap-2">
                <span className="font-medium">{branch.name}</span>
                {branch.is_main && (
                  <span className="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded">
                    Principal
                  </span>
                )}
              </div>
              {branch.address && (
                <p className="text-xs text-gray-500 mt-1 truncate">
                  {branch.address}
                </p>
              )}
            </div>
            {currentBranch.id === branch.id && (
              <Check className="h-4 w-4 text-blue-600" />
            )}
          </DropdownMenuItem>
        ))}
      </DropdownMenuContent>
    </DropdownMenu>
  )
}