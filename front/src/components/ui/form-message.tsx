import { cn } from "@/lib/utils"

interface FormMessageProps {
    type: 'success' | 'error'
    message: string
    className?: string
}
export function FormMessage({ type, message, className }: FormMessageProps) {
    return (
        <div className={cn(
            "p-3 rounded-md text-sm text-center",
            type === 'success'
                ? "bg-green-50 text-green-800 border border-green-200"
                : "bg-red-50 text-red-800 border border-red-200",
            className
        )}>
            {message}
        </div>
    )
}

interface FormErrorProps {
    message?: string
    className?: string
}
export function FormError({ message, className }: FormErrorProps) {
    if (!message) return null

    return (
        <p className={cn("text-sm text-red-600", className)}>
            {message}
        </p>
    )
}
