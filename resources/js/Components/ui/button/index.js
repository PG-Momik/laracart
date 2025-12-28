import {cva} from "class-variance-authority";

export {default as Button} from "./Button.vue";

export const buttonVariants = cva(
    "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all duration-200 ease-smooth focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring focus-visible:ring-offset-1 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0",
    {
        variants: {
            variant: {
                default:
                    "bg-primary text-primary-foreground shadow-subtle hover:bg-primary/95 hover:shadow-soft active:scale-[0.98] active:shadow-none",
                destructive:
                    "bg-destructive text-destructive-foreground shadow-subtle hover:bg-destructive/95 hover:shadow-soft active:scale-[0.98]",
                outline:
                    "border border-input/60 bg-background/50 backdrop-blur-sm shadow-subtle hover:bg-accent/50 hover:border-input hover:shadow-soft active:scale-[0.98]",
                secondary:
                    "bg-secondary/80 text-secondary-foreground shadow-subtle hover:bg-secondary hover:shadow-soft active:scale-[0.98]",
                ghost: "hover:bg-accent/50 hover:text-accent-foreground active:scale-[0.98]",
                link: "text-primary underline-offset-4 hover:underline hover:text-primary/80",
            },
            size: {
                default: "h-9 px-4 py-2",
                xs: "h-7 rounded px-2",
                sm: "h-8 rounded-md px-3 text-xs",
                lg: "h-10 rounded-md px-8",
                icon: "h-9 w-9",
                "icon-sm": "size-8",
                "icon-lg": "size-10",
            },
        },
        defaultVariants: {
            variant: "default",
            size: "default",
        },
    },
);
