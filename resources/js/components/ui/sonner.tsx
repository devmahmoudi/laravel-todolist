import { usePage } from "@inertiajs/react"
import { useTheme } from "next-themes"
import { useEffect } from "react"
import { Toaster as Sonner, ToasterProps, toast } from "sonner"

const Toaster = ({ ...props }: ToasterProps) => {
  const { theme = "system" } = useTheme()
  const { toasts } = usePage().props

  useEffect(() => {
    Object.entries(toasts).map(([key, value]) => {
      if (value)
        switch (key) {
          case "success":
            toast.success(value)
            break
          case "info":
            toast.info(value)
            break
          case "warning":
            toast.warning(value)
            break
          case "error":
            toast.error(value)
            break
        }
    })
  }, [toasts])

  return (
    <Sonner
      theme={theme as ToasterProps["theme"]}
      className="toaster group"
      style={
        {
          "--normal-bg": "var(--popover)",
          "--normal-text": "var(--popover-foreground)",
          "--normal-border": "var(--border)",
        } as React.CSSProperties
      }
      {...props}
    />
  )
}

export { Toaster }
