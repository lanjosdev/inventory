"use client"

import * as React from "react"
import Link from "next/link"
import { Store } from "lucide-react"

import {
  Sidebar,
  SidebarHeader,
  SidebarRail,
} from "@/components/ui/sidebar"

export function AppSidebar({ ...props }: React.ComponentProps<typeof Sidebar>) {
  return (
    <Sidebar collapsible="icon" {...props}>
      <SidebarHeader>
        <Link href="/dashboard" className="flex items-center justify-center p-2 hover:bg-sidebar-accent rounded-lg transition-colors">
          <div className="bg-primary rounded-lg p-2 mx-2">
            <Store className="h-4 w-4 text-primary-foreground" />
          </div>
        </Link>
      </SidebarHeader>

      

      <SidebarRail />
    </Sidebar>
  )
}
