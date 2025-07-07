'use client'

import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/components/ui/breadcrumb'
import { usePathname } from 'next/navigation'
import React from 'react'

export function DynamicBreadcrumb() {
    const pathname = usePathname()
    const segments = pathname.split('/').filter(Boolean)

    // Capitaliza a primeira letra de uma string
    const capitalize = (s: string) => s.charAt(0).toUpperCase() + s.slice(1)

    return (
        <Breadcrumb>
            <BreadcrumbList>
                <BreadcrumbItem>
                    <BreadcrumbLink href="/dashboard">Início</BreadcrumbLink>
                </BreadcrumbItem>
                {segments.map((segment, index) => {
                    const href = `/${segments.slice(0, index + 1).join('/')}`
                    const isLast = index === segments.length - 1

                    return (
                        <React.Fragment key={href}>
                            <BreadcrumbSeparator />
                            <BreadcrumbItem>
                                {isLast ? (
                                    <BreadcrumbPage>
                                        {capitalize(segment)}
                                    </BreadcrumbPage>
                                ) : (
                                    <BreadcrumbLink href={href}>
                                        {capitalize(segment)}
                                    </BreadcrumbLink>
                                )}
                            </BreadcrumbItem>
                        </React.Fragment>
                    )
                })}
            </BreadcrumbList>
        </Breadcrumb>
    )
}
