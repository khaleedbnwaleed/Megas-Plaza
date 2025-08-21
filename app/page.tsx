"use client"

import { useState } from "react"
import Link from "next/link"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import {
  Building2,
  Users,
  FileText,
  CreditCard,
  BarChart3,
  Shield,
  ArrowRight,
  CheckCircle,
  Star,
  Play,
  MapPin,
  Camera,
} from "lucide-react"

export default function Page() {
  const [activeTab, setActiveTab] = useState("overview")

  const features = [
    {
      title: "Smart User Management",
      description: "Role-based access control with secure authentication for tenants, managers, and administrators.",
      icon: Users,
      color: "text-chart-1",
    },
    {
      title: "Interactive Shop Catalog",
      description: "Advanced filtering, search, and detailed shop information with high-quality images and amenities.",
      icon: Building2,
      color: "text-chart-2",
    },
    {
      title: "Streamlined Applications",
      description:
        "Digital application process with document upload, real-time status tracking, and automated workflows.",
      icon: FileText,
      color: "text-chart-3",
    },
    {
      title: "Automated Billing",
      description: "Invoice generation, payment processing, and financial tracking with overdue management.",
      icon: CreditCard,
      color: "text-chart-4",
    },
    {
      title: "Analytics Dashboard",
      description: "Real-time insights, comprehensive reporting, and management tools with actionable data.",
      icon: BarChart3,
      color: "text-chart-5",
    },
    {
      title: "Enterprise Security",
      description: "CSRF protection, audit logging, and secure file handling with comprehensive data protection.",
      icon: Shield,
      color: "text-primary",
    },
  ]

  const testimonials = [
    {
      name: "Sarah Johnson",
      role: "Property Manager",
      company: "Metro Plaza",
      content: "This system transformed how we manage our shopping plaza. The automation saves us hours every week.",
      rating: 5,
    },
    {
      name: "Mike Chen",
      role: "Business Owner",
      company: "Tech Solutions Inc",
      content: "The application process was seamless, and the tenant portal makes managing our lease so easy.",
      rating: 5,
    },
    {
      name: "Lisa Rodriguez",
      role: "Plaza Administrator",
      company: "Central Square",
      content: "The analytics and reporting features give us insights we never had before. Highly recommended!",
      rating: 5,
    },
  ]

  const stats = [
    { label: "Active Shops", value: "150+", description: "Successfully managed" },
    { label: "Happy Tenants", value: "500+", description: "Across all locations" },
    { label: "Time Saved", value: "80%", description: "In administrative tasks" },
    { label: "Revenue Growth", value: "25%", description: "Average increase" },
  ]

  const plazaImages = [
    {
      url: "https://hebbkx1anhila5yf.public.blob.vercel-storage.com/WhatsApp%20Image%202025-08-18%20at%2015.32.00_d7ba001a.jpg-dvBRzJ2Yae2yKh4X0rZ0duaaj9RNNs.jpeg",
      title: "Main Plaza Building",
      description: "Two-story commercial complex with modern amenities",
    },
    {
      url: "https://hebbkx1anhila5yf.public.blob.vercel-storage.com/WhatsApp%20Image%202025-08-18%20at%2015.32.01_e38c7da5.jpg-wqFGu6owWvyjGHwnjCzbsBBpNJhgKM.jpeg",
      title: "Active Commercial Area",
      description: "Bustling marketplace with diverse businesses",
    },
    {
      url: "https://hebbkx1anhila5yf.public.blob.vercel-storage.com/WhatsApp%20Image%202025-08-18%20at%2015.32.00_c13a5ecf.jpg-9Wj2UxYgVQIqulVTyFvmZAAF8fCJey.jpeg",
      title: "Extended Plaza Complex",
      description: "Multiple connected shop units with consistent architecture",
    },
    {
      url: "https://hebbkx1anhila5yf.public.blob.vercel-storage.com/WhatsApp%20Image%202025-08-18%20at%2015.32.02_cf6f3660.jpg-EwG9oDZdThSgO2f4J9G9HNo6r2Jij3.jpeg",
      title: "SISU Solar Showroom",
      description: "Specialized business spaces under clear skies",
    },
    {
      url: "https://hebbkx1anhila5yf.public.blob.vercel-storage.com/WhatsApp%20Image%202025-08-18%20at%2015.32.02_506cc426.jpg-DRIYAlruvOowvLy8OfF7WXalPl3L9S.jpeg",
      title: "Diverse Business Hub",
      description: "From clothing to services - all business types welcome",
    },
  ]

  return (
    <div className="min-h-screen bg-background">
      {/* Header */}
      <header className="border-b border-border bg-card/50 backdrop-blur-sm sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between h-16">
            <div className="flex items-center space-x-3">
              <Building2 className="h-8 w-8 text-primary" />
              <div>
                <h1 className="text-xl font-bold text-foreground">Mega School Plaza</h1>
                <p className="text-xs text-muted-foreground">Shop Management Platform</p>
              </div>
            </div>
            <div className="flex items-center space-x-4">
              <Badge variant="secondary" className="bg-primary/10 text-primary border-primary/20">
                <CheckCircle className="h-3 w-3 mr-1" />
                Complete System
              </Badge>
              <Link href="/auth/login">
                <Button>Get Started</Button>
              </Link>
            </div>
          </div>
        </div>
      </header>

      {/* Hero Section */}
      <section className="relative py-20 lg:py-32 bg-gradient-to-br from-background via-muted/30 to-primary/5">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div className="text-center lg:text-left">
              <Badge variant="outline" className="mb-6 border-primary/20 text-primary">
                🚀 Complete PHP Solution
              </Badge>
              <h1 className="text-4xl lg:text-6xl font-bold text-foreground mb-6 leading-tight">
                Transform Your <span className="text-primary">Shop Rental</span> Management
              </h1>
              <p className="text-xl text-muted-foreground mb-8 max-w-2xl leading-relaxed">
                A comprehensive platform that streamlines shop rentals, tenant management, billing, and analytics. Built
                with modern PHP architecture and designed for scalability.
              </p>
              <div className="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start items-center">
                <Link href="/auth/login">
                  <Button size="lg" className="text-lg px-8 py-6">
                    Try Live Demo
                    <ArrowRight className="ml-2 h-5 w-5" />
                  </Button>
                </Link>
                <Button variant="outline" size="lg" className="text-lg px-8 py-6 bg-transparent">
                  <Play className="mr-2 h-5 w-5" />
                  Watch Overview
                </Button>
              </div>
            </div>

            <div className="relative">
              <div className="relative rounded-2xl overflow-hidden shadow-2xl">
                <img
                  src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/WhatsApp%20Image%202025-08-18%20at%2015.32.02_cf6f3660.jpg-EwG9oDZdThSgO2f4J9G9HNo6r2Jij3.jpeg"
                  alt="Mega School Plaza - Modern Commercial Complex"
                  className="w-full h-[400px] object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                <div className="absolute bottom-4 left-4 right-4">
                  <div className="bg-white/90 backdrop-blur-sm rounded-lg p-4">
                    <div className="flex items-center text-sm text-gray-700">
                      <MapPin className="h-4 w-4 mr-2 text-primary" />
                      <span className="font-medium">Mega School Plaza - Active Commercial Hub</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section className="py-20 bg-card">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <Badge variant="outline" className="mb-4 border-primary/20 text-primary">
              <Camera className="h-3 w-3 mr-1" />
              Our Plaza
            </Badge>
            <h2 className="text-3xl lg:text-4xl font-bold text-foreground mb-4">See Mega School Plaza in Action</h2>
            <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
              A thriving commercial complex with diverse businesses, modern architecture, and active community
              engagement.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            {plazaImages.map((image, index) => (
              <Card
                key={index}
                className="border-border overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1"
              >
                <div className="relative h-48 overflow-hidden">
                  <img
                    src={image.url || "/placeholder.svg"}
                    alt={image.title}
                    className="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                </div>
                <CardContent className="p-4">
                  <h3 className="font-semibold text-foreground mb-2">{image.title}</h3>
                  <p className="text-sm text-muted-foreground">{image.description}</p>
                </CardContent>
              </Card>
            ))}
          </div>

          <div className="text-center">
            <Card className="border-primary/20 bg-primary/5 inline-block">
              <CardContent className="p-6">
                <div className="flex items-center justify-center space-x-4">
                  <div className="text-center">
                    <div className="text-2xl font-bold text-primary">50+</div>
                    <div className="text-sm text-muted-foreground">Active Shops</div>
                  </div>
                  <div className="w-px h-12 bg-border"></div>
                  <div className="text-center">
                    <div className="text-2xl font-bold text-primary">2 Floors</div>
                    <div className="text-sm text-muted-foreground">Commercial Space</div>
                  </div>
                  <div className="w-px h-12 bg-border"></div>
                  <div className="text-center">
                    <div className="text-2xl font-bold text-primary">100%</div>
                    <div className="text-sm text-muted-foreground">Occupancy Rate</div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </section>

      {/* Stats Section */}
      <section className="py-16 bg-background">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-2 lg:grid-cols-4 gap-8">
            {stats.map((stat, index) => (
              <div key={index} className="text-center">
                <div className="text-3xl lg:text-4xl font-bold text-primary mb-2">{stat.value}</div>
                <div className="text-lg font-semibold text-foreground mb-1">{stat.label}</div>
                <div className="text-sm text-muted-foreground">{stat.description}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section className="py-20 bg-muted/30">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <Badge variant="outline" className="mb-4 border-primary/20 text-primary">
              ✨ Powerful Features
            </Badge>
            <h2 className="text-3xl lg:text-4xl font-bold text-foreground mb-4">
              Everything You Need to Manage Your Plaza
            </h2>
            <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
              From tenant applications to financial reporting, our platform handles every aspect of shop rental
              management.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {features.map((feature, index) => {
              const IconComponent = feature.icon
              return (
                <Card
                  key={index}
                  className="border-border hover:shadow-lg transition-all duration-300 hover:-translate-y-1"
                >
                  <CardHeader>
                    <div className={`w-12 h-12 rounded-lg bg-muted flex items-center justify-center mb-4`}>
                      <IconComponent className={`h-6 w-6 ${feature.color}`} />
                    </div>
                    <CardTitle className="text-xl">{feature.title}</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <CardDescription className="text-base leading-relaxed">{feature.description}</CardDescription>
                  </CardContent>
                </Card>
              )
            })}
          </div>
        </div>
      </section>

      {/* Demo Section */}
      <section className="py-20 bg-background">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <Badge variant="outline" className="mb-4 border-primary/20 text-primary">
              🎯 Interactive Demo
            </Badge>
            <h2 className="text-3xl lg:text-4xl font-bold text-foreground mb-4">Experience the Platform Live</h2>
            <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
              Try our fully functional demo with different user roles and explore all features hands-on.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <Link href="/auth/login" className="group">
              <Card className="border-border hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                <CardContent className="p-6 text-center">
                  <div className="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-primary/20 transition-colors">
                    <Shield className="h-8 w-8 text-primary" />
                  </div>
                  <h3 className="font-semibold text-foreground mb-2">Authentication</h3>
                  <p className="text-sm text-muted-foreground">Secure login with demo accounts</p>
                </CardContent>
              </Card>
            </Link>

            <Link href="/shops" className="group">
              <Card className="border-border hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                <CardContent className="p-6 text-center">
                  <div className="w-16 h-16 bg-chart-2/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-chart-2/20 transition-colors">
                    <Building2 className="h-8 w-8 text-chart-2" />
                  </div>
                  <h3 className="font-semibold text-foreground mb-2">Shop Catalog</h3>
                  <p className="text-sm text-muted-foreground">Browse and filter available shops</p>
                </CardContent>
              </Card>
            </Link>

            <Link href="/dashboard" className="group">
              <Card className="border-border hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                <CardContent className="p-6 text-center">
                  <div className="w-16 h-16 bg-chart-5/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-chart-5/20 transition-colors">
                    <BarChart3 className="h-8 w-8 text-chart-5" />
                  </div>
                  <h3 className="font-semibold text-foreground mb-2">Admin Dashboard</h3>
                  <p className="text-sm text-muted-foreground">Analytics and management tools</p>
                </CardContent>
              </Card>
            </Link>

            <Link href="/billing" className="group">
              <Card className="border-border hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                <CardContent className="p-6 text-center">
                  <div className="w-16 h-16 bg-chart-4/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-chart-4/20 transition-colors">
                    <CreditCard className="h-8 w-8 text-chart-4" />
                  </div>
                  <h3 className="font-semibold text-foreground mb-2">Billing System</h3>
                  <p className="text-sm text-muted-foreground">Invoice and payment management</p>
                </CardContent>
              </Card>
            </Link>
          </div>

          <Card className="border-primary/20 bg-primary/5">
            <CardContent className="p-8">
              <div className="text-center">
                <h3 className="text-2xl font-bold text-foreground mb-4">Ready to Get Started?</h3>
                <p className="text-muted-foreground mb-6 max-w-2xl mx-auto">
                  Use any of the demo accounts to explore the full functionality. Each role provides different access
                  levels and features.
                </p>
                <div className="flex flex-col sm:flex-row gap-4 justify-center">
                  <Link href="/auth/login">
                    <Button size="lg" className="px-8">
                      Start Demo
                      <ArrowRight className="ml-2 h-5 w-5" />
                    </Button>
                  </Link>
                  <Link href="/auth/register">
                    <Button variant="outline" size="lg" className="px-8 bg-transparent">
                      Create Account
                    </Button>
                  </Link>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </section>

      {/* Testimonials */}
      <section className="py-20 bg-muted/30">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <Badge variant="outline" className="mb-4 border-primary/20 text-primary">
              💬 Testimonials
            </Badge>
            <h2 className="text-3xl lg:text-4xl font-bold text-foreground mb-4">Trusted by Property Managers</h2>
            <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
              See what our users say about transforming their shop rental management.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {testimonials.map((testimonial, index) => (
              <Card key={index} className="border-border">
                <CardContent className="p-6">
                  <div className="flex items-center mb-4">
                    {[...Array(testimonial.rating)].map((_, i) => (
                      <Star key={i} className="h-4 w-4 text-yellow-400 fill-current" />
                    ))}
                  </div>
                  <p className="text-muted-foreground mb-4 italic">"{testimonial.content}"</p>
                  <div>
                    <div className="font-semibold text-foreground">{testimonial.name}</div>
                    <div className="text-sm text-muted-foreground">{testimonial.role}</div>
                    <div className="text-sm text-primary">{testimonial.company}</div>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-20 bg-gradient-to-r from-primary to-primary/80">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-3xl lg:text-4xl font-bold text-primary-foreground mb-4">
            Ready to Transform Your Business?
          </h2>
          <p className="text-xl text-primary-foreground/90 mb-8 max-w-2xl mx-auto">
            Join hundreds of property managers who have streamlined their operations with our comprehensive platform.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link href="/auth/login">
              <Button size="lg" variant="secondary" className="px-8 py-6 text-lg">
                Try Demo Now
                <ArrowRight className="ml-2 h-5 w-5" />
              </Button>
            </Link>
            <Link href="/auth/register">
              <Button
                size="lg"
                variant="outline"
                className="px-8 py-6 text-lg border-primary-foreground/20 text-primary-foreground hover:bg-primary-foreground/10 bg-transparent"
              >
                Get Started Free
              </Button>
            </Link>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-card border-t border-border">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
          <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div className="col-span-1 md:col-span-2">
              <div className="flex items-center space-x-3 mb-4">
                <Building2 className="h-8 w-8 text-primary" />
                <div>
                  <h3 className="text-xl font-bold text-foreground">Mega School Plaza</h3>
                  <p className="text-sm text-muted-foreground">Shop Management Platform</p>
                </div>
              </div>
              <p className="text-muted-foreground max-w-md">
                Complete shop rental and management solution built with modern PHP architecture, designed for
                scalability and ease of use.
              </p>
            </div>
            <div>
              <h4 className="font-semibold text-foreground mb-4">Features</h4>
              <ul className="space-y-2 text-muted-foreground">
                <li>User Management</li>
                <li>Shop Catalog</li>
                <li>Application System</li>
                <li>Billing & Payments</li>
              </ul>
            </div>
            <div>
              <h4 className="font-semibold text-foreground mb-4">Technology</h4>
              <ul className="space-y-2 text-muted-foreground">
                <li>PHP 8+</li>
                <li>MySQL Database</li>
                <li>Responsive Design</li>
                <li>Modern Architecture</li>
              </ul>
            </div>
          </div>
          <div className="border-t border-border mt-8 pt-8 text-center text-muted-foreground">
            <p>&copy; 2024 Mega School Plaza. Built with modern web technologies.</p>
          </div>
        </div>
      </footer>
    </div>
  )
}
