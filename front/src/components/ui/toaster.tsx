'use client'

import { Toaster as Sonner } from 'sonner'

type ToasterProps = React.ComponentProps<typeof Sonner>

const Toaster = ({ ...props }: ToasterProps) => {
  return (
    <Sonner
      className="toaster group"
      toastOptions={{
        classNames: {
          toast: '',
          description: '',
          actionButton: '',
          cancelButton: '',
        },
        unstyled: true,
      }}
      expand={true}
      richColors={false}
      closeButton={true}
      {...props}
    />
  )
}

export { Toaster }
