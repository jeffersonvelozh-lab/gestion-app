using AuthService.Data;
using AuthService.Models;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Infrastructure;

namespace AuthService.Data.Migrations;

[DbContext(typeof(AppDbContext))]
partial class AppDbContextModelSnapshot : ModelSnapshot
{
    protected override void BuildModel(ModelBuilder modelBuilder)
    {
        modelBuilder.HasAnnotation("ProductVersion", "8.0.0")
                    .HasAnnotation("Relational:MaxIdentifierLength", 128);

        modelBuilder.Entity<User>(b =>
        {
            b.HasKey(e => e.Id);
            b.HasIndex(e => e.Email).IsUnique();
            b.Property(e => e.Email).IsRequired().HasMaxLength(150);
            b.Property(e => e.Name).IsRequired().HasMaxLength(100);
            b.Property(e => e.PasswordHash).IsRequired();
            b.Property(e => e.Role).HasDefaultValue("user").HasMaxLength(20);
            b.ToTable("Users");
        });
    }
}
