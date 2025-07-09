import { AppSidebar } from '@/components/app-sidebar'
import { DynamicBreadcrumb } from '@/components/dynamic-breadcrumb'
import { HeaderUserMenu } from '@/components/header-user-menu'
import {
    SidebarInset,
    SidebarProvider,
    SidebarTrigger,
} from '@/components/ui/sidebar'
import { Separator } from "@/components/ui/separator"
import { getCurrentUser } from '@/lib/actions/authAction'


export default async function AuthLayout({
    children,
}: {
    children: React.ReactNode
}) {
    const user = await getCurrentUser()
    
    return (
        <SidebarProvider>
            <AppSidebar />

            <SidebarInset>
                <header className="sticky top-0 z-50 flex h-16 shrink-0 items-center gap-2 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12">
                    <div className="flex items-center gap-2 px-4 flex-1">
                        <SidebarTrigger className="-ml-1" />

                        <Separator orientation="vertical" className="mr-2 h-4" />

                        <DynamicBreadcrumb />
                    </div>
                    
                    {user && (
                        <div className="flex items-center gap-2 px-4">
                            <HeaderUserMenu user={{
                                name: user.name,
                                email: user.email,
                                avatar: '/placeholder-user.jpg'
                            }} />
                        </div>
                    )}
                </header>

                {children}
            </SidebarInset>
        </SidebarProvider>
    )
}
