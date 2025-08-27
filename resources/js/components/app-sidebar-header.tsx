import { Breadcrumbs } from '@/components/breadcrumbs';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { type BreadcrumbItem as BreadcrumbItemType, type SharedData } from '@/types';
import { usePage, router } from '@inertiajs/react';

export function AppSidebarHeader({ breadcrumbs = [] }: { breadcrumbs?: BreadcrumbItemType[] }) {
    const { props } = usePage<SharedData>();
    const { currentBranch, availableBranches } = props;
    
    const handleBranchChange = (branchId: string) => {
        router.post('/branch/switch', { branch_id: parseInt(branchId) }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                window.location.reload()
            }
        })
    }

    return (
        <header className="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/50 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4">
            <div className="flex items-center gap-2">
                <SidebarTrigger className="-ml-1" />
                <Breadcrumbs breadcrumbs={breadcrumbs} />
            </div>
            
            {/* Branch Selector in Header */}
            {currentBranch && availableBranches && availableBranches.length > 0 && (
                <div className="ml-auto flex items-center gap-2">
                    <span className="text-xs font-medium text-gray-600">🏥 Sucursal:</span>
                    <Select 
                        value={currentBranch.id.toString()} 
                        onValueChange={handleBranchChange}
                    >
                        <SelectTrigger className="w-64 h-9 text-sm">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            {availableBranches.map((branch) => (
                                <SelectItem key={branch.id} value={branch.id.toString()}>
                                    <div className="flex items-center gap-2">
                                        <span className="font-medium">{branch.name}</span>
                                        {branch.is_main && (
                                            <span className="text-xs bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded">
                                                Principal
                                            </span>
                                        )}
                                    </div>
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>
            )}
        </header>
    );
}
