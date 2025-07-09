"use client"

import * as React from "react"
import Link from "next/link"
import { usePathname } from "next/navigation"
import { Store, Home, Building2 } from "lucide-react"

import {
  Sidebar,
  SidebarHeader,
  SidebarContent,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarRail,
} from "@/components/ui/sidebar"

export function AppSidebar({ ...props }: React.ComponentProps<typeof Sidebar>) {
  const pathname = usePathname()

  const navigationItems = [
    {
      title: "Dashboard",
      url: "/dashboard",
      icon: Home,
    },
    {
      title: "Redes",
      url: "/redes",
      icon: Building2,
    },
  ]

  return (
    <Sidebar collapsible="icon" {...props}>
      <SidebarHeader>
        <Link href="/dashboard" className="flex items-center justify-center p-2 hover:bg-sidebar-accent rounded-lg transition-colors">
          <div className="bg-primary rounded-lg p-2 mx-2">
            <Store className="h-4 w-4 text-primary-foreground" />
          </div>
          
          <b className="group-data-[collapsible=icon]:hidden">Logo</b>
        </Link>
      </SidebarHeader>

      <SidebarContent>
        <SidebarMenu>
          {navigationItems.map((item) => (
            <SidebarMenuItem key={item.title}>
              <SidebarMenuButton
                asChild
                isActive={pathname === item.url}
                tooltip={item.title}
                className={`px-4 py-3 h-12 ${
                  pathname === item.url 
                    ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600 dark:bg-blue-950/50 dark:text-blue-400 dark:border-blue-400' 
                    : 'hover:bg-sidebar-accent'
                }`}
              >
                <Link href={item.url} className="flex items-center gap-3 w-full">
                  <item.icon className="h-6 w-6" />
                  <span className="text-base font-medium">{item.title}</span>
                </Link>
              </SidebarMenuButton>
            </SidebarMenuItem>
          ))}
        </SidebarMenu>
      </SidebarContent>

      <SidebarRail />
    </Sidebar>
  )
}
